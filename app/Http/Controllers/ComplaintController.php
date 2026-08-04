<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintItem;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $q = Complaint::with('items');

        if ($s = $request->input('q')) {
            $q->where(function ($w) use ($s) {
                $w->where('nama_customer', 'like', "%{$s}%")
                  ->orWhere('no_customer', 'like', "%{$s}%")
                  ->orWhereHas('items', function ($iq) use ($s) {
                      $iq->where('jenis_ketidaksesuaian', 'like', "%{$s}%")
                         ->orWhere('penyebab', 'like', "%{$s}%")
                         ->orWhere('detail_ketidaksesuaian', 'like', "%{$s}%");
                  });
            });
        }
        if ($d = $request->input('defect')) {
            $q->whereHas('items', fn ($iq) => $iq->where('jenis_ketidaksesuaian', $d));
        }
        if ($dd = $request->input('detail_defect')) {
            $q->whereHas('items', fn ($iq) => $iq->where('detail_ketidaksesuaian', $dd));
        }
        if ($st = $request->input('status')) {
            $q->where('status', $st);
        }
        if ($tahun = $request->integer('tahun')) {
            $q->whereYear('tanggal_complain', $tahun);
        }
        if ($bulan = $request->integer('bulan')) {
            $q->whereMonth('tanggal_complain', $bulan);
        }

        $complaints = $q->orderByDesc('tanggal_complain')->orderByDesc('id')->paginate(20)->withQueryString();

        // Daftar detail ketidaksesuaian untuk dropdown
        $detailDefects = ComplaintItem::query()
            ->whereNotNull('detail_ketidaksesuaian')
            ->where('detail_ketidaksesuaian', '!=', '')
            ->where('detail_ketidaksesuaian', '!=', '-')
            ->distinct()
            ->orderBy('detail_ketidaksesuaian')
            ->pluck('detail_ketidaksesuaian')
            ->all();

        // Daftar tahun untuk dropdown
        $years = Complaint::query()
            ->whereNotNull('tanggal_complain')
            ->selectRaw('YEAR(tanggal_complain) as y')
            ->distinct()
            ->orderBy('y')
            ->pluck('y')
            ->all();

        return view('complaints.index', [
            'complaints'    => $complaints,
            'defects'       => Complaint::KETIDAKSESUAIAN,
            'detailDefects' => $detailDefects,
            'years'         => $years,
        ]);
    }

    public function create()
    {
        return view('complaints.create', [
            'complaint'           => new Complaint(),
            'nextNoCustomer'      => Complaint::generateNoCustomer(),
            'noMeta'              => Complaint::noCustomerMeta(),
            'customers'           => $this->customerList(),
            'ketidaksesuaianList' => $this->optionList('jenis_ketidaksesuaian', Complaint::KETIDAKSESUAIAN),
            'penyebabList'        => $this->optionList('penyebab', Complaint::PENYEBAB),
        ]);
    }

    public function store(Request $request)
    {
        $data  = $this->validateData($request);
        $items = $this->resolveItems($request);
        $data['fishbone'] = $this->resolveFishbone($request, $items);

        $complaint = Complaint::create($data);

        foreach ($items as $item) {
            $complaint->items()->create($item);
        }

        return redirect()->route('complaints.index')
            ->with('success', "Complaint {$complaint->no_customer} berhasil ditambahkan. Hasil Apriori & grafik 7 Tools otomatis diperbarui.");
    }

    public function edit(Complaint $complaint)
    {
        $complaint->load('items');

        return view('complaints.edit', [
            'complaint'           => $complaint,
            'customers'           => $this->customerList(),
            'ketidaksesuaianList' => $this->optionList('jenis_ketidaksesuaian', Complaint::KETIDAKSESUAIAN),
            'penyebabList'        => $this->optionList('penyebab', Complaint::PENYEBAB),
        ]);
    }

    public function update(Request $request, Complaint $complaint)
    {
        $data  = $this->validateData($request);
        $items = $this->resolveItems($request);
        $data['fishbone'] = $this->resolveFishbone($request, $items);

        $complaint->update($data);

        // Hapus items lama, insert ulang
        $complaint->items()->delete();
        foreach ($items as $item) {
            $complaint->items()->create($item);
        }

        return redirect()->route('complaints.index')
            ->with('success', "Complaint {$complaint->no_customer} berhasil diperbarui.");
    }

    public function destroy(Complaint $complaint)
    {
        $no = $complaint->no_customer;
        $complaint->delete(); // cascade deletes items

        return redirect()->route('complaints.index')
            ->with('success', "Complaint {$no} dihapus.");
    }

    // ===================== Helpers =====================

    protected function customerList(): array
    {
        return Complaint::query()->select('nama_customer')->distinct()
            ->orderBy('nama_customer')->pluck('nama_customer')->all();
    }

    /** Gabungan kategori bawaan + kategori baru yang sudah pernah diinput user. */
    protected function optionList(string $column, array $defaults): array
    {
        $fromDb = ComplaintItem::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->where($column, '!=', '-')
            ->distinct()
            ->pluck($column)
            ->all();

        $merged = array_values(array_unique(array_merge($defaults, $fromDb)));
        sort($merged, SORT_NATURAL | SORT_FLAG_CASE);
        return $merged;
    }

    /**
     * Parse baris klasifikasi dari form → array items siap insert ke complaint_items.
     * Form mengirim: complaint_items[N][ket_val], [ket_new], [ket_det], [pen_val], [pen_new], [pen_det]
     *
     * @return array<int, array{jenis_ketidaksesuaian: ?string, detail_ketidaksesuaian: ?string, penyebab: ?string, detail_penyebab: ?string}>
     */
    protected function resolveItems(Request $request): array
    {
        $ketExisting = $this->optionList('jenis_ketidaksesuaian', Complaint::KETIDAKSESUAIAN);
        $penExisting = $this->optionList('penyebab', Complaint::PENYEBAB);

        $out = [];
        foreach ((array) $request->input('complaint_items', []) as $row) {
            // Ketidaksesuaian
            $ketRaw = ($row['ket_val'] ?? '') === '__new__' ? ($row['ket_new'] ?? '') : ($row['ket_val'] ?? '');
            $ketVal = Complaint::normalizeCategory($ketRaw, $ketExisting);
            $ketDet = trim((string) ($row['ket_det'] ?? ''));

            // Penyebab
            $penRaw = ($row['pen_val'] ?? '') === '__new__' ? ($row['pen_new'] ?? '') : ($row['pen_val'] ?? '');
            $penVal = Complaint::normalizeCategory($penRaw, $penExisting);
            $penDet = trim((string) ($row['pen_det'] ?? ''));

            // Skip baris kosong
            if (! $ketVal && $ketDet === '' && ! $penVal && $penDet === '') continue;

            $out[] = [
                'jenis_ketidaksesuaian'  => $ketVal,
                'detail_ketidaksesuaian' => $ketDet ?: null,
                'penyebab'               => $penVal,
                'detail_penyebab'        => $penDet ?: null,
            ];
        }

        return $out;
    }

    /** Susun fishbone 6M dari input textarea (satu sebab per baris). */
    protected function resolveFishbone(Request $request, array $items): array
    {
        $input = (array) $request->input('fishbone', []);
        $fb = [];
        foreach (Complaint::FISHBONE_CATEGORIES as $kat) {
            $raw = (string) ($input[$kat] ?? '');
            $fb[$kat] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw))));
        }
        if (! array_filter($fb)) {
            $penyebabList = array_values(array_unique(array_filter(array_column($items, 'penyebab'))));
            $fb = Complaint::buildFishboneFromPenyebab($penyebabList);
        }
        return $fb;
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            // no_customer & lead_time & status-default = OTOMATIS, tidak divalidasi dari input
            'nama_customer'      => ['required', 'string', 'max:255'],
            'tanggal_complain'   => ['required', 'date'],
            'ukuran'             => ['nullable', 'string', 'max:255'],
            'qty'                => ['nullable', 'integer', 'min:0'],
            'corrective_action'  => ['nullable', 'string'],
            'preventive_action'  => ['nullable', 'string'],
            'tanggal_kirim'      => ['nullable', 'date'],
            'tanggal_produksi'   => ['nullable', 'date'],
            'area'               => ['nullable', 'string', 'max:255'],
            'keterangan'         => ['nullable', 'string', 'max:255'],
            'status'             => ['required', 'in:Open,Close'],
            // Items divalidasi secara minimal — minimal 1 baris dengan ketidaksesuaian
            'complaint_items'              => ['required', 'array', 'min:1'],
            'complaint_items.*.ket_val'    => ['nullable', 'string'],
            'complaint_items.*.pen_val'    => ['nullable', 'string'],
        ], [], [
            'nama_customer'     => 'Nama Customer',
            'tanggal_complain'  => 'Tanggal Complain',
            'complaint_items'   => 'Klasifikasi Cacat',
        ]);
    }
}
