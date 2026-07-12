@php
    use App\Models\Complaint;
    $isEdit = $complaint->exists;
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" class="space-y-8">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <!-- Keterangan input vs otomatis -->
    <div class="flex flex-wrap gap-4 text-xs">
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-sky-500"></span> Diisi manual</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-400"></span> Terisi otomatis (tidak bisa diubah)</span>
    </div>

    <!-- Identitas -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Identitas Complaint</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span> No Customer (otomatis)
                </label>
                <input type="text" id="noCustomerPreview" value="{{ $isEdit ? $complaint->no_customer : $nextNoCustomer }}" disabled
                       class="w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-500 px-3 py-2 text-sm">
                @unless ($isEdit)
                    <p class="text-[11px] text-slate-400 mt-1">2 digit = kode customer (otomatis ikut nama existing), 3 digit = no transaksi. Final dibuat saat disimpan.</p>
                @endunless
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Nama Customer <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_customer" id="namaCustomer" list="customerList" required autocomplete="off"
                       value="{{ old('nama_customer', $complaint->nama_customer) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
                <datalist id="customerList">
                    @foreach ($customers ?? [] as $c)<option value="{{ $c }}">@endforeach
                </datalist>
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Tanggal Complain <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="tanggal_complain" required
                       value="{{ old('tanggal_complain', optional($complaint->tanggal_complain)->format('Y-m-d')) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            </div>
        </div>
    </div>

    <!-- Produk -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Detail Produk</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Ukuran
                </label>
                <input type="text" name="ukuran" placeholder="mis. 3 x 14 x 924"
                       value="{{ old('ukuran', $complaint->ukuran) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Qty (pcs)
                </label>
                <input type="number" name="qty" min="0" value="{{ old('qty', $complaint->qty) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Area / Lokasi
                </label>
                <input type="text" name="area" placeholder="mis. T3 / W2"
                       value="{{ old('area', $complaint->area) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            </div>
        </div>
    </div>

    <!-- Klasifikasi: 1 tabel, tiap baris = 1 cacat + 1 penyebab -->
    @php
        $ketOptions = $ketidaksesuaianList ?? [];
        $penOptions = $penyebabList ?? [];

        // Build rows from existing items (edit) or empty (create)
        $itemRows = [];
        if (old('complaint_items')) {
            foreach (old('complaint_items') as $row) {
                $itemRows[] = [
                    'ket_val' => $row['ket_val'] ?? '',
                    'ket_det' => $row['ket_det'] ?? '',
                    'pen_val' => $row['pen_val'] ?? '',
                    'pen_det' => $row['pen_det'] ?? '',
                ];
            }
        } elseif ($complaint->exists && $complaint->items->count()) {
            foreach ($complaint->items as $item) {
                $itemRows[] = [
                    'ket_val' => $item->jenis_ketidaksesuaian ?? '',
                    'ket_det' => $item->detail_ketidaksesuaian ?? '',
                    'pen_val' => $item->penyebab ?? '',
                    'pen_det' => $item->detail_penyebab ?? '',
                ];
            }
        }
        if (empty($itemRows)) {
            $itemRows[] = ['ket_val' => '', 'ket_det' => '', 'pen_val' => '', 'pen_det' => ''];
        }
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-1">Klasifikasi (dipakai Algoritma Apriori)</h3>
        <p class="text-xs text-slate-500 mb-4">Satu baris = satu jenis cacat dan penyebabnya. Gunakan tombol "+ Tambah Baris" jika ada lebih dari satu cacat.</p>

        <div id="itemList" class="space-y-3">
            @foreach ($itemRows as $i => $row)
                @include('complaints._class_row', [
                    'i'          => $i,
                    'ketOptions' => $ketOptions,
                    'penOptions' => $penOptions,
                    'ketVal'     => $row['ket_val'],
                    'ketDet'     => $row['ket_det'],
                    'penVal'     => $row['pen_val'],
                    'penDet'     => $row['pen_det'],
                ])
            @endforeach
        </div>
        <button type="button" id="addItemRow" class="mt-3 text-xs bg-sky-600 hover:bg-sky-700 text-white px-4 py-1.5 rounded-lg">+ Tambah Baris</button>
        <template id="itemRowTpl">
            @include('complaints._class_row', [
                'i'          => '__INDEX__',
                'ketOptions' => $ketOptions,
                'penOptions' => $penOptions,
                'ketVal'     => '',
                'ketDet'     => '',
                'penVal'     => '',
                'penDet'     => '',
            ])
        </template>
    </div>

    <!-- Rekomendasi otomatis -->
    <div id="recoPanel" class="hidden bg-gradient-to-br from-sky-50 to-indigo-50 border border-sky-200 rounded-xl p-5">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <h3 class="font-semibold text-sky-800 text-sm">Rekomendasi Tindakan (berdasarkan histori & Apriori)</h3>
            <span id="recoLoading" class="hidden text-xs text-slate-400">memuat…</span>
        </div>

        <div id="recoRules" class="mb-3 space-y-1"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-xs font-semibold text-slate-600 mb-1.5">Saran Corrective Action</div>
                <div id="recoCorrective" class="space-y-1.5"></div>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-600 mb-1.5">Saran Preventive Action</div>
                <div id="recoPreventive" class="space-y-1.5"></div>
            </div>
        </div>
    </div>

    <!-- Tindakan -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Tindakan & Penyelesaian</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Corrective Action
                </label>
                <textarea name="corrective_action" id="ta_corrective" rows="2"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">{{ old('corrective_action', $complaint->corrective_action) }}</textarea>
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Preventive Action
                </label>
                <textarea name="preventive_action" id="ta_preventive" rows="2"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">{{ old('preventive_action', $complaint->preventive_action) }}</textarea>
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Tanggal Produksi
                </label>
                <input type="date" name="tanggal_produksi"
                       value="{{ old('tanggal_produksi', optional($complaint->tanggal_produksi)->format('Y-m-d')) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Tanggal Kirim
                </label>
                <input type="date" name="tanggal_kirim"
                       value="{{ old('tanggal_kirim', optional($complaint->tanggal_kirim)->format('Y-m-d')) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Keterangan
                </label>
                <select name="keterangan"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
                    <option value="">— pilih —</option>
                    @foreach (Complaint::KETERANGAN as $opt)
                        <option value="{{ $opt }}" @selected(old('keterangan', $complaint->keterangan) === $opt)>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-xs font-medium text-slate-600 mb-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Status
                </label>
                <select name="status"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
                    @foreach (Complaint::STATUS as $opt)
                        <option value="{{ $opt }}" @selected(old('status', $complaint->status ?: 'Open') === $opt)>{{ $opt }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Default otomatis: Open. Ubah ke Close jika sudah selesai.</p>
            </div>
        </div>
        <p class="text-[11px] text-slate-400 mt-4">
            <span class="w-2 h-2 rounded-full bg-slate-400 inline-block"></span>
            <b>Lead time</b> (selisih hari Tanggal Complain − Tanggal Produksi) dihitung otomatis saat disimpan.
        </p>
    </div>

    <!-- Fishbone 6M (editable) -->
    @php
        $fb = $complaint->exists ? $complaint->fishboneNormalized() : array_fill_keys(\App\Models\Complaint::FISHBONE_CATEGORIES, []);
        $fbOld = old('fishbone');
        $fbLabel = ['Man' => 'Man / Manusia', 'Machine' => 'Machine / Mesin', 'Material' => 'Material',
                    'Method' => 'Method / Metode', 'Environment' => 'Environment / Lingkungan', 'Measurement' => 'Measurement / Pengukuran'];
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-sm font-semibold text-slate-700">Diagram Sebab-Akibat (Fishbone 6M)</h3>
            <button type="button" id="fbAutofill" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg border border-slate-300">
                ↻ Isi otomatis dari Penyebab
            </button>
        </div>
        <p class="text-xs text-slate-500 mb-4">Akibat = jenis ketidaksesuaian. Isi sebab pada tiap kategori 6M (satu sebab per baris). Bisa ditambah/diedit bebas.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach (\App\Models\Complaint::FISHBONE_CATEGORIES as $kat)
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ $fbLabel[$kat] ?? $kat }}</label>
                    <textarea name="fishbone[{{ $kat }}]" rows="3" data-fb="{{ $kat }}"
                              placeholder="satu sebab per baris…"
                              class="fb-area w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">{{ is_array($fbOld) ? ($fbOld[$kat] ?? '') : implode("\n", $fb[$kat] ?? []) }}</textarea>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Complaint' }}
        </button>
        <a href="{{ route('complaints.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Batal</a>
    </div>
</form>

@push('scripts')
<script>
(function () {
    const panel  = document.getElementById('recoPanel');
    const loading = document.getElementById('recoLoading');
    const elRules = document.getElementById('recoRules');
    const elCorr = document.getElementById('recoCorrective');
    const elPrev = document.getElementById('recoPreventive');
    const taCorr = document.getElementById('ta_corrective');
    const taPrev = document.getElementById('ta_preventive');
    const url = "{{ route('complaints.suggest') }}";

    const basisLabel = { kombinasi: 'kombinasi', penyebab: 'penyebab sama', jenis: 'defect sama' };

    function chip(text, count, basis) {
        return `<div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
            <div class="flex-1 text-sm text-slate-700">${text}
                <span class="block text-[10px] text-slate-400">${count}x · ${basisLabel[basis] || basis}</span>
            </div>
            <button type="button" data-fill="${encodeURIComponent(text)}"
                class="reco-use shrink-0 self-center text-[11px] bg-sky-600 hover:bg-sky-700 text-white px-2 py-1 rounded">Pakai</button>
        </div>`;
    }

    function render(data) {
        if (data.rules && data.rules.length) {
            elRules.innerHTML = data.rules.map(r =>
                `<div class="text-xs text-slate-600">🔗 ${r.interpretasi}</div>`).join('');
        } else {
            elRules.innerHTML = '<div class="text-xs text-slate-400">Tidak ada kaitan Apriori yang relevan.</div>';
        }
        elCorr.innerHTML = (data.corrective && data.corrective.length)
            ? data.corrective.map(a => chip(a.value, a.count, a.basis)).join('')
            : '<div class="text-xs text-slate-400">Belum ada histori.</div>';
        elPrev.innerHTML = (data.preventive && data.preventive.length)
            ? data.preventive.map(a => chip(a.value, a.count, a.basis)).join('')
            : '<div class="text-xs text-slate-400">Belum ada histori.</div>';

        elCorr.querySelectorAll('.reco-use').forEach(b =>
            b.addEventListener('click', () => { taCorr.value = decodeURIComponent(b.dataset.fill); }));
        elPrev.querySelectorAll('.reco-use').forEach(b =>
            b.addEventListener('click', () => { taPrev.value = decodeURIComponent(b.dataset.fill); }));
    }

    // ---- Item rows: unified ket+pen per row ----
    const itemList = document.getElementById('itemList');
    const tpl = document.getElementById('itemRowTpl');
    let rowIdx = itemList.querySelectorAll('.item-row').length;

    function syncNew(sel) {
        const ni = sel.closest('.space-y-2').querySelector('.cr-new');
        if (!ni) return;
        if (sel.value === '__new__') { ni.classList.remove('hidden'); ni.required = true; }
        else { ni.classList.add('hidden'); ni.required = false; ni.value = ''; }
    }

    function rowValue(sel) {
        if (sel.value === '__new__') {
            const ni = sel.closest('.space-y-2').querySelector('.cr-new');
            return ni ? ni.value.replace(/\s+/g, ' ').trim() : '';
        }
        return sel.value;
    }

    function distinctValues(cls) {
        const out = [];
        itemList.querySelectorAll(cls).forEach(sel => {
            const v = rowValue(sel);
            if (v && !out.some(x => x.toLowerCase() === v.toLowerCase())) out.push(v);
        });
        return out;
    }

    function updateRemoveButtons() {
        const rows = itemList.querySelectorAll('.item-row');
        rows.forEach(r => {
            const btn = r.querySelector('.item-remove');
            if (btn) btn.style.display = rows.length <= 1 ? 'none' : '';
        });
    }

    document.getElementById('addItemRow').addEventListener('click', () => {
        const html = tpl.innerHTML.replace(/__INDEX__/g, String(rowIdx++));
        const wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        itemList.appendChild(wrap.firstElementChild);
        updateRemoveButtons();
    });

    itemList.addEventListener('click', (e) => {
        if (e.target.closest('.item-remove')) {
            const rows = itemList.querySelectorAll('.item-row');
            if (rows.length > 1) { e.target.closest('.item-row').remove(); updateRemoveButtons(); fetchReco(); }
        }
    });
    itemList.addEventListener('change', (e) => { if (e.target.matches('.cr-select')) { syncNew(e.target); fetchReco(); } });
    itemList.addEventListener('input', (e) => { if (e.target.matches('.cr-new')) fetchReco(); });

    updateRemoveButtons();
    itemList.querySelectorAll('.cr-select').forEach(syncNew);

    let timer = null;
    function fetchReco() {
        const ket = distinctValues('.cr-ket-select');
        const pen = distinctValues('.cr-pen-select');
        if (!ket.length && !pen.length) { panel.classList.add('hidden'); return; }
        panel.classList.remove('hidden');
        loading.classList.remove('hidden');
        const params = new URLSearchParams();
        ket.forEach(v => params.append('ketidaksesuaian[]', v));
        pen.forEach(v => params.append('penyebab[]', v));
        clearTimeout(timer);
        timer = setTimeout(() => {
            fetch(`${url}?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => { loading.classList.add('hidden'); render(d); })
                .catch(() => { loading.classList.add('hidden'); elRules.innerHTML = '<div class="text-xs text-rose-500">Gagal memuat rekomendasi.</div>'; });
        }, 250);
    }

    if (distinctValues('.cr-ket-select').length || distinctValues('.cr-pen-select').length) fetchReco();

    // ---- Fishbone: isi otomatis dari penyebab ----
    const FISHBONE_6M = @json(\App\Models\Complaint::FISHBONE_6M);
    function kategoriOf(penyebab) {
        for (const [kat, list] of Object.entries(FISHBONE_6M)) {
            if (list.some(p => p.toLowerCase() === penyebab.toLowerCase())) return kat;
        }
        return 'Method';
    }
    const fbBtn = document.getElementById('fbAutofill');
    if (fbBtn) {
        fbBtn.addEventListener('click', () => {
            const buckets = {};
            distinctValues('.cr-pen-select').forEach(p => {
                const k = kategoriOf(p);
                (buckets[k] = buckets[k] || []).push(p);
            });
            document.querySelectorAll('.fb-area').forEach(area => {
                const kat = area.dataset.fb;
                const existing = area.value.split('\n').map(s => s.trim()).filter(Boolean);
                (buckets[kat] || []).forEach(p => { if (!existing.some(e => e.toLowerCase() === p.toLowerCase())) existing.push(p); });
                area.value = existing.join('\n');
            });
        });
    }

    // ---- No Customer: preview ikut kode customer existing saat nama dipilih/diketik ----
@unless ($isEdit)
    const noMeta = @json($noMeta ?? null);
    const nameInput = document.getElementById('namaCustomer');
    const noPrev = document.getElementById('noCustomerPreview');
    if (noMeta && nameInput && noPrev) {
        const updateNo = () => {
            const key = (nameInput.value || '').replace(/\s+/g, ' ').trim().toLowerCase();
            const code = (key && noMeta.codeByCustomer[key]) ? noMeta.codeByCustomer[key] : noMeta.nextNewCode;
            noPrev.value = code + '-' + noMeta.nextTrans;
        };
        nameInput.addEventListener('input', updateNo);
        nameInput.addEventListener('change', updateNo);
        updateNo();
    }
@endunless
})();
</script>
@endpush
