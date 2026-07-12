<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintItem;
use Illuminate\Support\Collection;

/**
 * Agregasi data untuk Seven Tools Quality Control.
 * Semua perhitungan dilakukan live dari tabel complaints + complaint_items,
 * sehingga setiap data baru langsung memengaruhi grafik.
 */
class SevenToolsService
{
    protected Collection $data;

    public function __construct(?Collection $data = null)
    {
        $this->data = $data ?? Complaint::with('items')->get();
    }

    public static function make(?Collection $data = null): self
    {
        return new self($data);
    }

    /**
     * Hitung frekuensi kemunculan untuk field tertentu dari complaint_items.
     * Satu complaint dengan 2 cacat menyumbang 2 kemunculan.
     *
     * @param string $field 'jenis_ketidaksesuaian' atau 'penyebab'
     * @return Collection<string,int> [nilai => jumlah], terurut desc
     */
    protected function countItemField(string $field): Collection
    {
        $counts = [];
        foreach ($this->data as $c) {
            foreach ($c->items as $item) {
                $v = trim((string) $item->{$field});
                if ($v === '' || $v === '-') continue;
                $counts[$v] = ($counts[$v] ?? 0) + 1;
            }
        }
        return collect($counts)->sortDesc();
    }

    /** Ringkasan KPI untuk halaman dashboard. */
    public function kpi(): array
    {
        $total = $this->data->count();
        $open  = $this->data->where('status', 'Open')->count();
        $close = $this->data->where('status', 'Close')->count();
        $totalQty = (int) $this->data->sum('qty');

        $topDefect = $this->countItemField('jenis_ketidaksesuaian');
        $topCause  = $this->countItemField('penyebab');

        $avgLead = round((float) $this->data->whereNotNull('lead_time')->avg('lead_time'), 1);

        return [
            'total_complaint' => $total,
            'open'            => $open,
            'close'           => $close,
            'persen_close'    => $total ? round($close / $total * 100, 1) : 0,
            'total_qty'       => $totalQty,
            'avg_lead_time'   => $avgLead,
            'top_defect'      => $topDefect->keys()->first(),
            'top_defect_n'    => $topDefect->first(),
            'top_cause'       => $topCause->keys()->first(),
            'top_cause_n'     => $topCause->first(),
            'jumlah_customer' => $this->data->pluck('nama_customer')->unique()->count(),
        ];
    }

    // ====== 1. CHECK SHEET (tabulasi jenis ketidaksesuaian x bulan) ======
    public function checkSheet(): array
    {
        $bulanLabels = $this->bulanLabels();

        // defect => [Y-m => jumlah]; satu complaint bisa menyumbang ke beberapa defect
        $matrix = [];
        $grandTotal = 0;
        foreach ($this->data as $c) {
            $k = $c->tanggal_complain?->format('Y-m');
            foreach ($c->items as $item) {
                $defect = trim((string) $item->jenis_ketidaksesuaian);
                if ($defect === '' || $defect === '-') continue;
                if (! isset($matrix[$defect])) {
                    $matrix[$defect] = array_fill_keys(array_keys($bulanLabels), 0);
                }
                if ($k !== null && isset($matrix[$defect][$k])) {
                    $matrix[$defect][$k]++;
                }
                $grandTotal++;
            }
        }

        $rows = [];
        foreach ($matrix as $defect => $perBulan) {
            $rows[] = [
                'kategori'  => $defect,
                'per_bulan' => array_values($perBulan),
                'total'     => array_sum($perBulan),
            ];
        }
        usort($rows, fn ($a, $b) => $b['total'] <=> $a['total']);

        // total kemunculan defect per bulan
        $totalPerBulan = array_fill_keys(array_keys($bulanLabels), 0);
        foreach ($matrix as $perBulan) {
            foreach ($perBulan as $bln => $n) $totalPerBulan[$bln] += $n;
        }

        return [
            'bulan' => array_values($bulanLabels),
            'rows'  => $rows,
            'total_per_bulan' => array_values($totalPerBulan),
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * Saring data berdasarkan periode tanggal_complain (untuk filter Pareto).
     * Tahun/bulan null = tidak disaring pada bagian itu.
     */
    public static function filterPeriode(Collection $data, ?int $year, ?int $month): Collection
    {
        if (! $year && ! $month) return $data;

        return $data->filter(function ($c) use ($year, $month) {
            $d = $c->tanggal_complain;
            if (! $d) return false;
            if ($year && (int) $d->format('Y') !== $year) return false;
            if ($month && (int) $d->format('n') !== $month) return false;
            return true;
        })->values();
    }

    /**
     * Paket Pareto + metadata filter (daftar tahun tersedia, periode terpilih, jumlah baris).
     * Dipakai dashboard & halaman Seven Tools agar Pareto bisa difilter per bulan/tahun.
     */
    public static function paretoBundle(Collection $data, ?int $year, ?int $month, string $field = 'jenis_ketidaksesuaian'): array
    {
        $years = $data->pluck('tanggal_complain')->filter()
            ->map(fn ($d) => (int) $d->format('Y'))->unique()->sort()->values()->all();

        $filtered = self::filterPeriode($data, $year, $month);

        return [
            'pareto' => self::make($filtered)->pareto($field),
            'filter' => [
                'year'  => $year,
                'month' => $month,
                'years' => $years,
                'count' => $filtered->count(),
            ],
        ];
    }

    // ====== 2. PARETO (jenis ketidaksesuaian + kumulatif %) ======
    public function pareto(string $field = 'jenis_ketidaksesuaian'): array
    {
        $counts = $this->countItemField($field);

        $total = $counts->sum();
        $labels = [];
        $values = [];
        $cumulative = [];
        $run = 0;
        foreach ($counts as $label => $count) {
            $labels[] = $label;
            $values[] = $count;
            $run += $count;
            $cumulative[] = $total ? round($run / $total * 100, 1) : 0;
        }

        return [
            'labels'     => $labels,
            'values'     => $values,
            'cumulative' => $cumulative,
            'total'      => $total,
        ];
    }

    // ====== 2b. PARETO CUSTOMER (frekuensi complaint per customer + kumulatif %) ======
    public function paretoCustomer(): array
    {
        $counts = $this->data
            ->groupBy('nama_customer')
            ->map->count()
            ->sortDesc();

        $total = $counts->sum();
        $labels = [];
        $values = [];
        $cumulative = [];
        $run = 0;
        foreach ($counts as $label => $count) {
            $labels[] = $label;
            $values[] = $count;
            $run += $count;
            $cumulative[] = $total ? round($run / $total * 100, 1) : 0;
        }

        return [
            'labels'     => $labels,
            'values'     => $values,
            'cumulative' => $cumulative,
            'total'      => $total,
        ];
    }

    /**
     * Paket Pareto Customer + metadata filter (daftar tahun tersedia, periode terpilih, jumlah baris).
     */
    public static function paretoBundleCustomer(Collection $data, ?int $year, ?int $month): array
    {
        $years = $data->pluck('tanggal_complain')->filter()
            ->map(fn ($d) => (int) $d->format('Y'))->unique()->sort()->values()->all();

        $filtered = self::filterPeriode($data, $year, $month);

        return [
            'pareto' => self::make($filtered)->paretoCustomer(),
            'filter' => [
                'year'  => $year,
                'month' => $month,
                'years' => $years,
                'count' => $filtered->count(),
            ],
        ];
    }

    // ====== 3. HISTOGRAM (distribusi Qty per complaint, dibin) ======
    public function histogram(int $bins = 8): array
    {
        $qty = $this->data->whereNotNull('qty')->pluck('qty')
            ->map(fn ($v) => (int) $v)->filter(fn ($v) => $v > 0)->values();

        if ($qty->isEmpty()) {
            return ['labels' => [], 'values' => [], 'min' => 0, 'max' => 0];
        }

        $min = $qty->min();
        $max = $qty->max();
        $range = max(1, $max - $min);
        $width = (int) ceil($range / $bins);
        $width = max(1, $width);

        $labels = [];
        $values = array_fill(0, $bins, 0);
        for ($i = 0; $i < $bins; $i++) {
            $lo = $min + $i * $width;
            $hi = $lo + $width - 1;
            $labels[] = number_format($lo) . '–' . number_format($hi);
        }
        foreach ($qty as $v) {
            $idx = (int) floor(($v - $min) / $width);
            if ($idx >= $bins) $idx = $bins - 1;
            if ($idx < 0) $idx = 0;
            $values[$idx]++;
        }

        return ['labels' => $labels, 'values' => $values, 'min' => $min, 'max' => $max];
    }

    // ====== 4. CONTROL CHART (c-chart: jumlah complaint per bulan) ======
    public function controlChart(): array
    {
        $bulanLabels = $this->bulanLabels();
        $perBulan = array_fill_keys(array_keys($bulanLabels), 0);
        foreach ($this->data as $it) {
            $k = $it->tanggal_complain?->format('Y-m');
            if ($k !== null && isset($perBulan[$k])) {
                $perBulan[$k]++;
            }
        }
        $values = array_values($perBulan);
        $n = count($values);
        $mean = $n ? array_sum($values) / $n : 0;        // CL
        $sigma = sqrt(max(0, $mean));                      // c-chart: sigma = sqrt(c-bar)
        $ucl = $mean + 3 * $sigma;
        $lcl = max(0, $mean - 3 * $sigma);

        // titik out-of-control
        $ooc = [];
        foreach ($values as $i => $v) {
            if ($v > $ucl || $v < $lcl) $ooc[] = $i;
        }

        return [
            'labels' => array_values($bulanLabels),
            'values' => $values,
            'cl'     => round($mean, 2),
            'ucl'    => round($ucl, 2),
            'lcl'    => round($lcl, 2),
            'ooc'    => $ooc,
        ];
    }

    // ====== 5. SCATTER (Qty vs Lead Time) ======
    public function scatter(): array
    {
        $points = $this->data
            ->filter(fn ($c) => $c->qty !== null && $c->lead_time !== null)
            ->map(fn ($c) => [
                'x' => (int) $c->qty,
                'y' => (int) $c->lead_time,
                'label' => $c->nama_customer . ' (' . $c->items->pluck('jenis_ketidaksesuaian')->filter()->unique()->implode(', ') . ')',
            ])->values()->all();

        return [
            'points' => $points,
            'x_title' => 'Qty Complaint (pcs)',
            'y_title' => 'Lead Time (hari): Tgl Complain − Tgl Produksi',
        ];
    }

    // ====== 6. FISHBONE / ISHIKAWA (penyebab dikelompokkan 6M) ======
    public function fishbone(): array
    {
        $causeCounts = $this->countItemField('penyebab');

        $categories = [];
        $assigned = [];
        foreach (Complaint::FISHBONE_6M as $kategori => $penyebabList) {
            $causes = [];
            foreach ($penyebabList as $p) {
                $n = (int) ($causeCounts[$p] ?? 0);
                if ($n > 0) {
                    $causes[] = ['nama' => $p, 'jumlah' => $n];
                    $assigned[$p] = true;
                }
            }
            usort($causes, fn ($a, $b) => $b['jumlah'] <=> $a['jumlah']);
            $categories[] = [
                'kategori' => $kategori,
                'total' => array_sum(array_column($causes, 'jumlah')),
                'causes' => $causes,
            ];
        }

        // penyebab yang belum termasuk 6M -> kategori "Lainnya"
        $lain = [];
        foreach ($causeCounts as $p => $n) {
            if (! isset($assigned[$p]) && $n > 0) {
                $lain[] = ['nama' => $p, 'jumlah' => (int) $n];
            }
        }
        if (! empty($lain)) {
            usort($lain, fn ($a, $b) => $b['jumlah'] <=> $a['jumlah']);
            $categories[] = ['kategori' => 'Lainnya', 'total' => array_sum(array_column($lain, 'jumlah')), 'causes' => $lain];
        }

        $efek = $this->countItemField('jenis_ketidaksesuaian')->keys()->first();

        return [
            'efek' => $efek ?? 'Customer Complaint / NCR',
            'categories' => $categories,
        ];
    }

    // ====== 7. STRATIFIKASI (per Area, per Customer, per Penyebab) ======
    public function stratifikasi(): array
    {
        $perArea = $this->data
            ->filter(fn ($c) => $c->area !== null && trim((string) $c->area) !== '' && trim((string) $c->area) !== '-')
            ->groupBy('area')->map->count()->sortDesc()->take(10);

        $perCustomer = $this->data
            ->groupBy('nama_customer')->map->count()->sortDesc()->take(10);

        $perKeterangan = $this->data->whereNotNull('keterangan')
            ->groupBy('keterangan')->map->count()->sortDesc();

        $perPenyebab = $this->countItemField('penyebab');

        return [
            'area'       => ['labels' => $perArea->keys()->all(), 'values' => array_values($perArea->all())],
            'customer'   => ['labels' => $perCustomer->keys()->all(), 'values' => array_values($perCustomer->all())],
            'keterangan' => ['labels' => $perKeterangan->keys()->all(), 'values' => array_values($perKeterangan->all())],
            'penyebab'   => ['labels' => $perPenyebab->keys()->all(), 'values' => array_values($perPenyebab->all())],
        ];
    }

    // ===================== helper =====================

    /** Daftar bulan (Y-m => label) dari rentang data. */
    protected function bulanLabels(): array
    {
        $dates = $this->data->pluck('tanggal_complain')->filter();
        if ($dates->isEmpty()) return [];

        $min = $dates->min();
        $max = $dates->max();
        $labels = [];
        $cursor = $min->copy()->startOfMonth();
        $end = $max->copy()->startOfMonth();
        $namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        while ($cursor <= $end) {
            $labels[$cursor->format('Y-m')] = $namaBulan[$cursor->month - 1] . ' ' . $cursor->format('y');
            $cursor->addMonth();
        }
        return $labels;
    }

    protected function totalPerBulan(array $bulanLabels): array
    {
        $perBulan = array_fill_keys(array_keys($bulanLabels), 0);
        foreach ($this->data as $it) {
            $k = $it->tanggal_complain?->format('Y-m');
            if ($k !== null && isset($perBulan[$k])) {
                $perBulan[$k]++;
            }
        }
        return array_values($perBulan);
    }
}
