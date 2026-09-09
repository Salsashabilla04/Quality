<?php

namespace App\Services;

use App\Models\ComplaintItem;
use Illuminate\Support\Collection;

/**
 * Implementasi Algoritma Apriori murni di PHP (mengikuti logika apriori_complain.py).
 *
 * Item dibentuk dari kolom "jenis_ketidaksesuaian" dan "penyebab" di tabel complaint_items.
 * Menghasilkan frequent itemsets (berdasarkan min support) dan
 * association rules (berdasarkan min confidence) lengkap dengan lift,
 * kekuatan kaitan, dan interpretasi bahasa Indonesia.
 */
class AprioriService
{
    protected float $minSupport;
    protected float $minConfidence;

    /** @var array<int, array<int, string>> daftar transaksi (tiap transaksi = list item) */
    protected array $transactions = [];

    /** @var Collection|null simpan raw items untuk extract detail breakdown */
    protected ?Collection $rawItems = null;

    /**
     * Cache hasil perhitungan Apriori menggunakan Laravel Cache (Memory/Redis).
     * Mencegah pemrosesan ulang (on-the-fly) yang berat jika data komplain sangat besar.
     *
     * @return array{rules: array, frequent: array, totalTrx: int, calculated_at: string}
     */
    public static function getCachedResult(float $minSupport = 0.05, float $minConfidence = 0.5): array
    {
        $key = "apriori_results_v2_{$minSupport}_{$minConfidence}";

        return \Illuminate\Support\Facades\Cache::remember($key, now()->addHours(2), function () use ($minSupport, $minConfidence) {
            $service = (new self($minSupport, $minConfidence))->buildTransactions();
            $frequent = $service->frequentItemsets();
            $rules = $service->associationRules($frequent);

            return [
                'rules'         => $rules,
                'frequent'      => $frequent,
                'totalTrx'      => $service->transactionCount(),
                'calculated_at' => now()->toDateTimeString(),
            ];
        });
    }

    /**
     * Hapus Cache Apriori otomatis ketika ada data komplain baru ditambah/diperbarui/dihapus.
     */
    public static function clearCache(float $minSupport = 0.05, float $minConfidence = 0.5): void
    {
        \Illuminate\Support\Facades\Cache::forget("apriori_results_{$minSupport}_{$minConfidence}");
    }

    public function __construct(float $minSupport = 0.05, float $minConfidence = 0.5)
    {
        $this->minSupport = $minSupport;
        $this->minConfidence = $minConfidence;
    }

    /**
     * Bentuk transaksi dari tabel complaint_items, di-groupBy complaint_id.
     * Tiap group (complaint_id) = 1 transaksi/basket berisi semua cacat & penyebab.
     *
     * @param Collection|null $items jika null, query dari DB langsung
     */
    public function buildTransactions(?Collection $items = null): self
    {
        $this->rawItems = $items ?? ComplaintItem::all();
        $grouped = $this->rawItems->groupBy('complaint_id');

        $this->transactions = [];
        foreach ($grouped as $complaintId => $group) {
            $basket = [];
            foreach ($group as $item) {
                $ket = is_array($item) ? ($item['jenis_ketidaksesuaian'] ?? null) : $item->jenis_ketidaksesuaian;
                $pen = is_array($item) ? ($item['penyebab'] ?? null) : $item->penyebab;

                if ($this->valid($ket)) $basket[] = 'Ketidaksesuaian=' . trim($ket);
                if ($this->valid($pen)) $basket[] = 'Penyebab=' . trim($pen);
            }
            if (! empty($basket)) {
                $this->transactions[] = array_values(array_unique($basket));
            }
        }
        return $this;
    }

    protected function valid($v): bool
    {
        if ($v === null) return false;
        $v = strtolower(trim((string) $v));
        return ! in_array($v, ['', '-', 'nan', 'null'], true);
    }

    public function transactionCount(): int
    {
        return count($this->transactions);
    }

    /**
     * Hitung frequent itemsets level demi level (Apriori).
     *
     * @return array<int, array{items: string[], support: float, count: int, size: int}>
     */
    public function frequentItemsets(): array
    {
        $n = count($this->transactions);
        if ($n === 0) return [];

        $minCount = $this->minSupport * $n;
        $allFrequent = [];

        // ---- Level 1 ----
        $count1 = [];
        foreach ($this->transactions as $t) {
            foreach ($t as $item) {
                $count1[$item] = ($count1[$item] ?? 0) + 1;
            }
        }

        $current = []; // frequent k-itemsets sebagai array string item (sorted)
        foreach ($count1 as $item => $cnt) {
            if ($cnt >= $minCount) {
                $key = $item;
                $current[$key] = [$item];
                $allFrequent[] = [
                    'items'   => [$item],
                    'count'   => $cnt,
                    'support' => $cnt / $n,
                    'size'    => 1,
                ];
            }
        }

        // ---- Level k >= 2 ----
        $k = 2;
        while (! empty($current)) {
            $candidates = $this->generateCandidates(array_values($current), $k);
            if (empty($candidates)) break;

            // hitung support tiap candidate
            $countK = [];
            foreach ($candidates as $key => $cand) {
                $countK[$key] = 0;
            }
            foreach ($this->transactions as $t) {
                $tset = array_flip($t);
                foreach ($candidates as $key => $cand) {
                    $contains = true;
                    foreach ($cand as $item) {
                        if (! isset($tset[$item])) { $contains = false; break; }
                    }
                    if ($contains) $countK[$key]++;
                }
            }

            $next = [];
            foreach ($candidates as $key => $cand) {
                if ($countK[$key] >= $minCount) {
                    $next[$key] = $cand;
                    $allFrequent[] = [
                        'items'   => $cand,
                        'count'   => $countK[$key],
                        'support' => $countK[$key] / $n,
                        'size'    => $k,
                    ];
                }
            }

            $current = $next;
            $k++;
        }

        // urutkan support desc
        usort($allFrequent, fn ($a, $b) => $b['support'] <=> $a['support']);
        return $allFrequent;
    }

    /**
     * Gabungkan frequent (k-1)-itemsets menjadi candidate k-itemsets.
     */
    protected function generateCandidates(array $frequentPrev, int $k): array
    {
        $candidates = [];
        $m = count($frequentPrev);
        for ($i = 0; $i < $m; $i++) {
            for ($j = $i + 1; $j < $m; $j++) {
                $union = array_values(array_unique(array_merge($frequentPrev[$i], $frequentPrev[$j])));
                if (count($union) === $k) {
                    sort($union);
                    $key = implode('||', $union);
                    $candidates[$key] = $union;
                }
            }
        }
        return $candidates;
    }

    /**
     * Bentuk association rules dari frequent itemsets (size >= 2).
     *
     * @return array<int, array<string, mixed>>
     */
    public function associationRules(?array $frequent = null): array
    {
        $frequent = $frequent ?? $this->frequentItemsets();
        $n = count($this->transactions);
        if ($n === 0) return [];

        // index support per itemset-key untuk lookup
        $supportMap = [];
        foreach ($frequent as $f) {
            $key = $this->key($f['items']);
            $supportMap[$key] = $f['support'];
        }

        $rules = [];
        foreach ($frequent as $f) {
            if ($f['size'] < 2) continue;
            $items = $f['items'];
            $supportAll = $f['support'];

            // semua subset non-kosong sebagai antecedent
            foreach ($this->properSubsets($items) as $antecedent) {
                $consequent = array_values(array_diff($items, $antecedent));
                if (empty($consequent)) continue;

                $supA = $supportMap[$this->key($antecedent)] ?? null;
                $supC = $supportMap[$this->key($consequent)] ?? null;
                if ($supA === null || $supC === null || $supA == 0) continue;

                $confidence = $supportAll / $supA;
                if ($confidence < $this->minConfidence) continue;

                $lift = $confidence / $supC;

                $antClean = $this->bersih($antecedent);
                $conClean = $this->bersih($consequent);

                // Deteksi jenis item (Ketidaksesuaian vs Penyebab) untuk label UI yang logis
                $antHasKet = false; $antHasPen = false;
                foreach ($antecedent as $ai) {
                    if (str_starts_with($ai, 'Ketidaksesuaian=')) $antHasKet = true;
                    if (str_starts_with($ai, 'Penyebab=')) $antHasPen = true;
                }
                $conHasKet = false; $conHasPen = false;
                foreach ($consequent as $ci) {
                    if (str_starts_with($ci, 'Ketidaksesuaian=')) $conHasKet = true;
                    if (str_starts_with($ci, 'Penyebab=')) $conHasPen = true;
                }

                if ($antHasKet && $conHasPen) {
                    $ruleType  = 'defect_to_cause';
                    $antLabel  = 'JIKA TERJADI CACAT';
                    $conLabel  = 'MAKA AKAR PENYEBABNYA';
                    $arrowText = 'Peluang Berakar Dari';
                    $saranText = "Fokuskan perbaikan preventif pada faktor <strong class=\"text-slate-900\">{$conClean}</strong> untuk menekan timbulnya cacat <strong class=\"text-slate-900\">{$antClean}</strong>.";
                } elseif ($antHasPen && $conHasKet) {
                    $ruleType  = 'cause_to_defect';
                    $antLabel  = 'JIKA FAKTOR PENYEBAB';
                    $conLabel  = 'MAKA BERDAMPAK CACAT';
                    $arrowText = 'Berdampak Memicu Cacat';
                    $saranText = "Fokuskan perbaikan preventif pada faktor <strong class=\"text-slate-900\">{$antClean}</strong> untuk menekan timbulnya cacat <strong class=\"text-slate-900\">{$conClean}</strong>.";
                } elseif ($antHasKet && $conHasKet) {
                    $ruleType  = 'defect_to_defect';
                    $antLabel  = 'JIKA TERJADI CACAT';
                    $conLabel  = 'MAKA SERING DISERTAI CACAT';
                    $arrowText = 'Cenderung Diikuti Cacat';
                    $saranText = "Pemeriksaan komprehensif pada cacat <strong class=\"text-slate-900\">{$antClean}</strong> dan <strong class=\"text-slate-900\">{$conClean}</strong>.";
                } else {
                    $ruleType  = 'general';
                    $antLabel  = 'JIKA TERJADI FAKTOR';
                    $conLabel  = 'MAKA TERHUBUNG FAKTOR';
                    $arrowText = 'Terkait Dengan';
                    $saranText = "Perbaiki faktor <strong class=\"text-slate-900\">{$conClean}</strong> dan <strong class=\"text-slate-900\">{$antClean}</strong> secara berkala.";
                }

                $rules[] = [
                    'antecedents'      => $antClean,
                    'consequents'      => $conClean,
                    'rule_type'        => $ruleType,
                    'ant_label'        => $antLabel,
                    'con_label'        => $conLabel,
                    'arrow_text'       => $arrowText,
                    'saran_text'       => $saranText,
                    'support'          => round($supportAll, 6),
                    'confidence'       => round($confidence, 6),
                    'lift'             => round($lift, 6),
                    'kekuatan'         => $this->kekuatan($lift),
                    'interpretasi'     => $this->interpretasi(
                        $antClean,
                        $conClean,
                        $confidence,
                        $lift
                    ),
                    'detail_breakdown' => $this->extractDetailBreakdown($antClean, $conClean),
                ];
            }
        }

        // urutkan lift desc
        usort($rules, fn ($a, $b) => $b['lift'] <=> $a['lift']);
        return $rules;
    }

    /**
     * Ekstrak frekuensi detail ketidaksesuaian & detail penyebab real dari data complaint_items.
     */
    protected function extractDetailBreakdown(string $antecedent, string $consequent): array
    {
        if (! $this->rawItems || $this->rawItems->isEmpty()) {
            return ['detail_ketidaksesuaian' => [], 'detail_penyebab' => [], 'total_matching' => 0];
        }

        $matching = $this->rawItems->filter(function ($item) use ($antecedent, $consequent) {
            $ket = trim((string) ($item->jenis_ketidaksesuaian ?? ''));
            $pen = trim((string) ($item->penyebab ?? ''));

            $aMatch = ($ket !== '' && (str_contains($antecedent, $ket) || str_contains($consequent, $ket)));
            $pMatch = ($pen !== '' && (str_contains($antecedent, $pen) || str_contains($consequent, $pen)));

            return $aMatch && $pMatch;
        });

        if ($matching->isEmpty()) {
            $matching = $this->rawItems->filter(function ($item) use ($antecedent, $consequent) {
                $ket = trim((string) ($item->jenis_ketidaksesuaian ?? ''));
                $pen = trim((string) ($item->penyebab ?? ''));
                return ($ket !== '' && str_contains($antecedent, $ket)) || ($pen !== '' && str_contains($consequent, $pen));
            });
        }

        $totalMatching = $matching->count();
        $divisor = $totalMatching > 0 ? $totalMatching : 1;

        // Detail Ketidaksesuaian
        $detailKetCounts = [];
        foreach ($matching as $item) {
            $dk = trim((string) ($item->detail_ketidaksesuaian ?? ''));
            if ($dk !== '' && $dk !== '-') {
                $detailKetCounts[$dk] = ($detailKetCounts[$dk] ?? 0) + 1;
            }
        }
        arsort($detailKetCounts);

        $topDetailKet = [];
        foreach (array_slice($detailKetCounts, 0, 3, true) as $dk => $cnt) {
            $topDetailKet[] = [
                'detail'  => $dk,
                'count'   => $cnt,
                'percent' => round(($cnt / $divisor) * 100, 1),
            ];
        }

        // Detail Penyebab
        $detailPenCounts = [];
        foreach ($matching as $item) {
            $dp = trim((string) ($item->detail_penyebab ?? ''));
            if ($dp !== '' && $dp !== '-') {
                $detailPenCounts[$dp] = ($detailPenCounts[$dp] ?? 0) + 1;
            }
        }
        arsort($detailPenCounts);

        $topDetailPen = [];
        foreach (array_slice($detailPenCounts, 0, 3, true) as $dp => $cnt) {
            $topDetailPen[] = [
                'detail'  => $dp,
                'count'   => $cnt,
                'percent' => round(($cnt / $divisor) * 100, 1),
            ];
        }

        return [
            'detail_ketidaksesuaian' => $topDetailKet,
            'detail_penyebab'        => $topDetailPen,
            'total_matching'         => $totalMatching,
        ];
    }

    /** Semua proper subset non-kosong (selain himpunan penuh). */
    protected function properSubsets(array $items): array
    {
        $subsets = [];
        $total = count($items);
        $max = (1 << $total) - 1;
        for ($mask = 1; $mask < $max; $mask++) {
            $subset = [];
            for ($i = 0; $i < $total; $i++) {
                if ($mask & (1 << $i)) $subset[] = $items[$i];
            }
            $subsets[] = $subset;
        }
        return $subsets;
    }

    protected function key(array $items): string
    {
        $copy = $items;
        sort($copy);
        return implode('||', $copy);
    }

    /** Hilangkan prefix kolom agar tampil rapi. */
    protected function bersih(array $items): string
    {
        $clean = array_map(
            fn ($i) => str_replace(['Ketidaksesuaian=', 'Penyebab='], '', $i),
            $items
        );
        sort($clean);
        return implode(', ', $clean);
    }

    protected function kekuatan(float $lift): string
    {
        if ($lift >= 5) return 'Sangat Kuat';
        if ($lift >= 3) return 'Kuat';
        if ($lift >= 1.5) return 'Sedang';
        return 'Lemah';
    }

    protected function interpretasi(string $a, string $c, float $conf, float $lift): string
    {
        return sprintf(
            'Dari semua kasus "%s", sebanyak %d%% terkait dengan "%s". Kaitan keduanya %s (lift %.1fx).',
            $a, round($conf * 100), $c, strtolower($this->kekuatan($lift)), $lift
        );
    }
}
