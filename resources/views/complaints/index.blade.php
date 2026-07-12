@extends('layouts.app')
@section('title', 'Data Complaint')
@section('subtitle', 'Daftar seluruh Customer Complaint & Non-Conformance Report')

@section('actions')
    <a href="{{ route('export.complaints.excel') }}"
       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        Export Excel
    </a>
@endsection

@section('content')

<form method="GET" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="customer / defect / penyebab"
               class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-64 focus:ring-sky-500 focus:border-sky-500">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Jenis Ketidaksesuaian</label>
        <select name="defect" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            <option value="">Semua</option>
            @foreach ($defects as $d)
                <option value="{{ $d }}" @selected(request('defect')===$d)>{{ $d }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
        <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            <option value="">Semua</option>
            <option value="Open" @selected(request('status')==='Open')>Open</option>
            <option value="Close" @selected(request('status')==='Close')>Close</option>
        </select>
    </div>
    <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
    @if (request()->hasAny(['q','defect','status']))
        <a href="{{ route('complaints.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Reset</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase tracking-wide">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Tgl Complain</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Ukuran</th>
                    <th class="px-4 py-3 text-right">Qty</th>
                    <th class="px-4 py-3">Ketidaksesuaian</th>
                    <th class="px-4 py-3">Penyebab</th>
                    <th class="px-4 py-3 text-right">Lead</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($complaints as $c)
                    @php
                        $ketTags = $c->items->pluck('jenis_ketidaksesuaian')->filter()->unique()->values();
                        $penTags = $c->items->pluck('penyebab')->filter()->unique()->values();
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $c->no_customer }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ optional($c->tanggal_complain)->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-medium text-slate-700">{{ $c->nama_customer }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $c->ukuran }}</td>
                        <td class="px-4 py-3 text-right">{{ $c->qty ? number_format($c->qty) : '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($ketTags as $k)
                                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded">{{ $k }}</span>
                                @empty
                                    <span class="text-xs text-slate-300">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($penTags as $p)
                                    <span class="text-xs text-slate-600">{{ $p }}{{ ! $loop->last ? ',' : '' }}</span>
                                @empty
                                    <span class="text-xs text-slate-300">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-xs text-slate-500">{{ $c->lead_time !== null ? $c->lead_time.' hr' : '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-[11px] px-2 py-0.5 rounded-full {{ $c->status==='Close' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $c->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="row-menu relative inline-block text-left">
                                <button type="button" class="menu-btn p-1.5 rounded-lg hover:bg-slate-100 text-slate-500">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zM10 11.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zM10 17a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                    </svg>
                                </button>
                                <div class="menu-panel hidden absolute right-0 mt-1 w-44 bg-white border border-slate-200 rounded-lg shadow-lg z-20 py-1">
                                    <a href="{{ route('complaints.edit', $c) }}" class="block px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">✏️ Edit</a>
                                    <button type="button"
                                        class="ncr-dl w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"
                                        data-action="{{ route('complaints.ncr', $c) }}"
                                        data-no="{{ $c->no_customer }}"
                                        data-penyebab="{{ $penTags->implode(', ') }}"
                                        data-correction="{{ $c->corrective_action }}"
                                        data-corrective="{{ $c->preventive_action }}"
                                        data-fishbone='@json($c->fishboneNormalized())'>📄 Download PDF</button>
                                    <form action="{{ route('complaints.destroy', $c) }}" method="POST"
                                          onsubmit="return confirm('Hapus complaint {{ $c->no_customer }}?')">
                                        @csrf @method('DELETE')
                                        <button class="w-full text-left px-3 py-2 text-sm text-rose-600 hover:bg-rose-50">🗑️ Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="px-4 py-10 text-center text-slate-400">Belum ada data complaint.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $complaints->links() }}</div>

{{-- ===== Modal override sebelum download NCR PDF ===== --}}
<div id="ncrModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40" data-close></div>
    <div class="absolute inset-0 flex items-start justify-center p-6 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl my-6">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <div>
                    <h3 class="font-semibold text-slate-800">Edit isi sebelum cetak — <span id="ncrNo" class="text-sky-600"></span></h3>
                    <p class="text-xs text-slate-500">Perubahan di sini hanya untuk PDF. <b>Data asli tidak berubah.</b></p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 text-xl" data-close>&times;</button>
            </div>
            <form id="ncrForm" method="POST" action="" target="_blank" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Penyebab / Root Cause</label>
                    <textarea name="penyebab" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tindakan Koreksi / Correction Action</label>
                        <textarea name="correction" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tindakan Korektif / Corrective Action</label>
                        <textarea name="corrective" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500"></textarea>
                    </div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-600 mb-2">Lampiran Fishbone 6M (satu sebab per baris)</div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @foreach (\App\Models\Complaint::FISHBONE_CATEGORIES as $kat)
                            <div>
                                <label class="block text-[11px] font-medium text-slate-500 mb-1">{{ $kat }}</label>
                                <textarea name="fishbone[{{ $kat }}]" data-fb="{{ $kat }}" rows="2" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:ring-sky-500 focus:border-sky-500"></textarea>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" class="text-sm text-slate-500 hover:text-slate-700" data-close>Batal</button>
                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">Download PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // ----- kebab menu -----
    document.querySelectorAll('.row-menu').forEach(menu => {
        const btn = menu.querySelector('.menu-btn');
        const panel = menu.querySelector('.menu-panel');
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const open = !panel.classList.contains('hidden');
            document.querySelectorAll('.menu-panel').forEach(p => p.classList.add('hidden'));
            if (!open) panel.classList.remove('hidden');
        });
    });
    document.addEventListener('click', () => document.querySelectorAll('.menu-panel').forEach(p => p.classList.add('hidden')));

    // ----- modal NCR -----
    const modal = document.getElementById('ncrModal');
    const form = document.getElementById('ncrForm');
    function openModal() { modal.classList.remove('hidden'); }
    function closeModal() { modal.classList.add('hidden'); }
    modal.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', closeModal));

    document.querySelectorAll('.ncr-dl').forEach(btn => {
        btn.addEventListener('click', () => {
            form.action = btn.dataset.action;
            document.getElementById('ncrNo').textContent = btn.dataset.no || '';
            form.querySelector('[name=penyebab]').value = btn.dataset.penyebab || '';
            form.querySelector('[name=correction]').value = btn.dataset.correction || '';
            form.querySelector('[name=corrective]').value = btn.dataset.corrective || '';
            let fb = {};
            try { fb = JSON.parse(btn.dataset.fishbone || '{}'); } catch (e) {}
            form.querySelectorAll('[data-fb]').forEach(area => {
                const arr = fb[area.dataset.fb] || [];
                area.value = Array.isArray(arr) ? arr.join('\n') : '';
            });
            openModal();
        });
    });
    // tutup modal setelah submit (PDF terbuka di tab baru)
    form.addEventListener('submit', () => setTimeout(closeModal, 400));
})();
</script>
@endpush
@endsection
