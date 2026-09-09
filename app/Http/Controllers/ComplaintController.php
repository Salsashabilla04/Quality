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

        if (auth()->check() && auth()->user()->role === 'customer') {
            $q->where('nama_customer', auth()->user()->name);
        }

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
        if ($request->boolean('revisi')) {
            $q->where('supervisor_approval', 'Rejected');
        }
        if ($appFilter = $request->input('approval')) {
            $q->where('supervisor_approval', $appFilter);
        }
        if ($request->boolean('anomali')) {
            $q->whereNotNull('lead_time')
              ->where(function ($w) {
                  $w->where('lead_time', '<', 0)
                    ->orWhere('lead_time', '>', 100);
              });
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
        \Illuminate\Support\Facades\Gate::authorize('submit-complaints');

        return view('complaints.create', [
            'complaint'                    => new Complaint(),
            'nextNoCustomer'               => Complaint::generateNoCustomer(),
            'noMeta'                       => Complaint::noCustomerMeta(),
            'customers'                    => $this->customerList(),
            'ketidaksesuaianList'          => $this->optionList('jenis_ketidaksesuaian', Complaint::KETIDAKSESUAIAN),
            'penyebabList'                 => $this->optionList('penyebab', Complaint::PENYEBAB),
            'detailKetidaksesuaianOptions' => $this->detailOptionList('detail_ketidaksesuaian'),
            'detailPenyebabOptions'        => $this->detailOptionList('detail_penyebab'),
        ]);
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('submit-complaints');

        if (!$request->has('status')) {
            $request->merge(['status' => 'Open']);
        }
        if (!$request->input('tanggal_complain')) {
            $request->merge(['tanggal_complain' => now()->toDateString()]);
        }

        $data  = $this->validateData($request);
        $data['user_id'] = auth()->id();
        $items = $this->resolveItems($request);
        $data['fishbone'] = $this->resolveFishbone($request, $items);
        $data['deskripsi_customer'] = $request->input('deskripsi_customer');
        $data['deskripsi_penyebab'] = $request->input('deskripsi_penyebab');

        $complaint = Complaint::create($data);

        foreach ($items as $item) {
            $complaint->items()->create($item);
        }

        Complaint::recalculateTransactionNumbers();
        $complaint->refresh();

        \App\Services\AprioriService::clearCache();

        return redirect()->route('complaints.index')
            ->with('success', "Complaint {$complaint->no_customer} berhasil ditambahkan. Hasil Apriori & grafik 7 Tools otomatis diperbarui.");
    }

    public function approveNcr(Request $request, Complaint $complaint)
    {
        \Illuminate\Support\Facades\Gate::authorize('approve-ncr');

        $request->validate([
            'approval_status'    => ['required', 'in:Approved,Rejected,Pending'],
            'corrective_action'  => ['nullable', 'string'],
            'preventive_action'  => ['nullable', 'string'],
            'catatan_supervisor' => ['nullable', 'string', 'max:1000'],
            'keterangan'         => ['nullable', 'in:Feedback,Retur'],
            'perlu_visit'        => ['nullable', 'boolean'],
            'tanggal_visit'      => ['nullable', 'date', 'after_or_equal:today'],
            'jam_visit'          => ['nullable', 'date_format:H:i'],
            'catatan_visit'      => ['nullable', 'string', 'max:1000'],
        ]);

        $status = $request->input('approval_status');

        $updateData = [
            'supervisor_approval' => $status,
            'catatan_supervisor'  => $request->input('catatan_supervisor'),
            'approved_at'         => now(),
            'perlu_visit'         => $request->boolean('perlu_visit'),
            'tanggal_visit'       => $request->input('tanggal_visit'),
            'jam_visit'           => $request->input('jam_visit'),
            'catatan_visit'       => $request->input('catatan_visit'),
        ];

        // Jika SPV menyetujui (Approved), status otomatis menjadi Close (Selesai) dan simpan keterangan
        if ($status === 'Approved') {
            $updateData['status']      = 'Close';
            $updateData['keterangan']  = $request->input('keterangan'); // Feedback / Retur
        } elseif ($status === 'Rejected') {
            $updateData['status'] = 'Open';
        }

        if ($request->has('corrective_action')) {
            $updateData['corrective_action'] = $request->input('corrective_action');
        }
        if ($request->has('preventive_action')) {
            $updateData['preventive_action'] = $request->input('preventive_action');
        }

        $complaint->update($updateData);

        $keteranganText = '';
        if ($status === 'Approved' && $request->input('keterangan')) {
            $keteranganText = ' [' . $request->input('keterangan') . ']';
        }
        $text = $status === 'Approved'
            ? "Disetujui & Kasus Close ✅{$keteranganText}"
            : ($status === 'Rejected' ? 'Ditolak untuk Revisi QA ❌' : 'Diperbarui');
        if ($request->boolean('perlu_visit')) {
            $text .= ' + Scheduled Customer Visit 🚐';
        }

        return redirect()->back()
            ->with('success', "Validasi CAPA Supervisor untuk NCR {$complaint->no_customer} berhasil disimpan: {$text}");
    }

    public function ajukanValidasi(Request $request, Complaint $complaint)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-complaints');

        $complaint->update([
            'status'              => 'Diproses',
            'supervisor_approval' => 'Pending',
        ]);

        return redirect()->back()
            ->with('success', "Kasus komplain {$complaint->no_customer} berhasil diajukan ke Supervisor QC untuk validasi CAPA & penerbitan NCR.");
    }

    public function edit(Complaint $complaint)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-complaints');

        $complaint->load('items');

        return view('complaints.edit', [
            'complaint'                    => $complaint,
            'customers'                    => $this->customerList(),
            'ketidaksesuaianList'          => $this->optionList('jenis_ketidaksesuaian', Complaint::KETIDAKSESUAIAN),
            'penyebabList'                 => $this->optionList('penyebab', Complaint::PENYEBAB),
            'detailKetidaksesuaianOptions' => $this->detailOptionList('detail_ketidaksesuaian'),
            'detailPenyebabOptions'        => $this->detailOptionList('detail_penyebab'),
        ]);
    }

    public function update(Request $request, Complaint $complaint)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-complaints');

        $data  = $this->validateData($request);
        $items = $this->resolveItems($request);
        $data['fishbone'] = $this->resolveFishbone($request, $items);
        $data['deskripsi_customer'] = $request->input('deskripsi_customer', $complaint->deskripsi_customer);
        $data['deskripsi_penyebab'] = $request->input('deskripsi_penyebab', $complaint->deskripsi_penyebab);

        $complaint->update($data);

        // Hapus items lama, insert ulang
        $complaint->items()->delete();
        foreach ($items as $item) {
            $complaint->items()->create($item);
        }

        Complaint::recalculateTransactionNumbers();
        $complaint->refresh();

        \App\Services\AprioriService::clearCache();

        return redirect()->route('complaints.index')
            ->with('success', "Complaint {$complaint->no_customer} berhasil diperbarui.");
    }

    public function quickClose(Request $request, Complaint $complaint)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-complaints');

        $complaint->update(['status' => 'Close']);
        
        Complaint::recalculateTransactionNumbers();
        
        return back()->with('success', "Complaint {$complaint->no_customer} berhasil ditutup (Close).");
    }

    public function destroy(Complaint $complaint)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-complaints');

        $no = $complaint->no_customer;
        $complaint->delete(); // cascade deletes items

        Complaint::recalculateTransactionNumbers();
        \App\Services\AprioriService::clearCache();

        return redirect()->route('complaints.index')
            ->with('success', "Complaint {$no} dihapus.");
    }

    // ===================== Helpers =====================

    protected function customerList(): array
    {
        return Complaint::query()->select('nama_customer')->distinct()
            ->orderBy('nama_customer')->pluck('nama_customer')->all();
    }

    /** Daftar detail ketidaksesuaian/penyebab yang pernah di-input (untuk auto-suggest datalist). */
    protected function detailOptionList(string $column): array
    {
        return ComplaintItem::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->where($column, '!=', '-')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->all();
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

            // Server-side fallback: jika detail diisi tapi jenis kosong, resolve dari kamus sinonim
            if (! $ketVal && $ketDet !== '') {
                $resolved = Complaint::resolveFromKeyword($ketDet);
                if ($resolved['jenis']) {
                    $ketVal = $resolved['jenis'];
                }
                // Jika detail cocok sinonim awam, simpan detail kanonik (bersih)
                if ($resolved['detail'] && mb_strtolower($ketDet) !== mb_strtolower($resolved['detail'])) {
                    $ketDet = $resolved['detail'];
                }
            }

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
            'nama_customer'      => ['required', 'string', 'max:255'],
            'tanggal_complain'   => ['required', 'date'],
            'ukuran'             => ['nullable', 'string', 'max:255'],
            'qty'                => ['nullable', 'integer', 'min:0'],
            'corrective_action'  => ['nullable', 'string'],
            'preventive_action'  => ['nullable', 'string'],
            'tanggal_kirim'      => ['nullable', 'date'],
            'tanggal_produksi'   => ['nullable', 'date'],
            'area'               => ['nullable', 'string', 'max:255'],
            'deskripsi_customer' => ['nullable', 'string', 'max:2000'],
            'deskripsi_penyebab' => ['nullable', 'string', 'max:2000'],
            'keterangan'         => ['nullable', 'string', 'max:255'],
            'status'             => ['nullable', 'in:Open,Diproses,Close'],
            'perlu_visit'        => ['nullable', 'boolean'],
            'tanggal_visit'      => ['nullable', 'date', 'after_or_equal:today'],
            'jam_visit'          => ['nullable', 'date_format:H:i'],
            'catatan_visit'      => ['nullable', 'string', 'max:1000'],
            'complaint_items'              => ['nullable', 'array'],
            'complaint_items.*.ket_val'    => ['nullable', 'string'],
            'complaint_items.*.pen_val'    => ['nullable', 'string'],
        ], [], [
            'nama_customer'     => 'Nama Customer',
            'tanggal_complain'  => 'Tanggal Complain',
            'complaint_items'   => 'Klasifikasi Cacat',
        ]);
    }
}
