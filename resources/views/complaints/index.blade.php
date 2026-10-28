@extends('layouts.app')
@section('title', 'Data Complaint & NCR')
@section('subtitle', 'Monitoring, investigasi QA, dan persetujuan (approval) Supervisor QC')

@section('actions')
    <div class="flex items-center gap-2">
        @can('manage-complaints')
            <button
                type="button"
                onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="btn btn-warning btn-sm shrink-0 whitespace-nowrap"
            >
                <i class="fas fa-file-import"></i>
                <span>Import Excel</span>
            </button>
        @endcan

        @can('manage-complaints')
            <a
                href="{{ route('export.complaints.excel', request()->query()) }}"
                class="btn btn-success btn-sm shrink-0 whitespace-nowrap"
            >
                <i class="fas fa-file-excel"></i>
                <span>
                    Export Excel
                    @if (request()->hasAny([
                        'q',
                        'status',
                        'tahun',
                        'bulan',
                        'anomali',
                        'revisi',
                        'approval'
                    ]))
                        (Terfilter)
                    @endif
                </span>
            </a>
        @endcan
    </div>
@endsection

@section('content')

@php
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                 7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

    $revisiCount = \App\Models\Complaint::where('supervisor_approval', 'Rejected')->count();
    $pendingSpvCount = \App\Models\Complaint::where('supervisor_approval', 'Pending')->count();
@endphp

@can('manage-complaints')
    @if($revisiCount > 0)
    <div class="mb-4 bg-white border-l-4 border-l-[#e74a3b] rounded shadow-sm p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-rose-100 text-[#e74a3b] flex items-center justify-center font-bold text-lg shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-gray-800">PERHATIAN QA: {{ $revisiCount }} Dokumen NCR Membutuhkan Perbaikan / Revisi dari Supervisor!</div>
                <div class="text-xs text-gray-500 mt-0.5">Supervisor QC meminta perbaikan pada analisis atau tindakan perbaikan. Klik tombol di kanan untuk memfilter dan memperbaiki kasus.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['revisi' => 1]) }}" class="btn btn-danger btn-sm shrink-0 whitespace-nowrap">
            <i class="fas fa-undo-alt"></i>
            <span>Lihat Kasus Perlu Revisi ({{ $revisiCount }})</span>
        </a>
    </div>
    @endif
@endcan

@can('approve-ncr')
    @if($pendingSpvCount > 0)
    <div class="mb-4 bg-white border-l-4 border-l-[#f6c23e] rounded shadow-sm p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-amber-100 text-[#f6c23e] flex items-center justify-center font-bold text-lg shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-gray-800">MEMBUTUHKAN VALIDASI: {{ $pendingSpvCount }} Dokumen NCR Menunggu Persetujuan Anda!</div>
                <div class="text-xs text-gray-500 mt-0.5">Silakan tinjau data komplain dan klik tombol "Validasi SPV" pada baris komplain di bawah.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}" class="btn btn-warning btn-sm shrink-0 whitespace-nowrap">
            <i class="fas fa-clock"></i>
            <span>Lihat Kasus Pending Approval ({{ $pendingSpvCount }})</span>
        </a>
    </div>
    @endif
@endcan

<form method="GET" class="bg-white rounded shadow-sm border border-gray-200 p-5 mb-5 flex flex-col gap-4">
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[250px]">
            <label class="block text-xs font-bold text-gray-600 mb-1">Cari</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik nama customer, jenis defect, detail, atau penyebab..."
                   class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs h-9 focus:ring-[#4e73df] focus:border-[#4e73df]">
        </div>
        
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Status QA</label>
            <select name="status" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs h-9 focus:ring-[#4e73df] focus:border-[#4e73df] w-36 bg-white">
                <option value="">Semua Status</option>
                <option value="Open" @selected(request('status')==='Open')>Open (Baru)</option>
                <option value="Diproses" @selected(request('status')==='Diproses')>Diproses QA</option>
                <option value="Close" @selected(request('status')==='Close')>Close (Selesai)</option>
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Tahun</label>
            <select name="tahun" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs h-9 focus:ring-[#4e73df] focus:border-[#4e73df] w-28 bg-white">
                <option value="">Semua</option>
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected(request()->integer('tahun') === (int) $y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Bulan</label>
            <select name="bulan" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs h-9 focus:ring-[#4e73df] focus:border-[#4e73df] w-32 bg-white">
                <option value="">Semua</option>
                @foreach ($namaBulan as $n => $label)
                    <option value="{{ $n }}" @selected(request()->integer('bulan') === $n)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="flex flex-wrap gap-2.5 items-center pt-3 border-t border-gray-100">
        <label class="inline-flex items-center gap-2 cursor-pointer bg-rose-50 border border-rose-200 px-3 h-8 rounded-md hover:bg-rose-100 transition-colors">
            <input type="checkbox" name="anomali" value="1" @checked(request('anomali')) class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
            <span class="text-xs font-bold text-rose-700 flex items-center gap-1.5">
                <i class="fas fa-exclamation-triangle fa-sm text-rose-600"></i>
                <span>Filter Anomali Lead Time</span>
            </span>
        </label>
        
        <div class="flex-1"></div>

        @if (request()->hasAny(['q','defect','detail_defect','status','tahun','bulan','anomali']))
            <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary btn-sm h-8 rounded-md">
                <i class="fas fa-undo"></i>
                <span>Reset</span>
            </a>
        @endif
        <button class="btn btn-primary btn-sm h-8 rounded-md">
            <i class="fas fa-filter"></i>
            <span>Terapkan Filter</span>
        </button>
    </div>
</form>

<style>
    /* Cegah dropdown aksi tertutup oleh baris tabel di bawahnya (Stacking Context Fix) */
    tr.menu-active {
        position: relative !important;
        z-index: 40 !important;
    }
    tr.menu-active td {
        z-index: 40 !important;
    }
    tr:has(.menu-panel:not(.hidden)) {
        position: relative !important;
        z-index: 40 !important;
    }
    tr:has(.menu-panel:not(.hidden)) td {
        z-index: 40 !important;
    }
    .row-menu .menu-panel {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.18), 0 8px 10px -6px rgba(0, 0, 0, 0.12) !important;
    }
</style>

<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    <div class="overflow-x-auto min-h-[320px]">
        <table class="w-full min-w-[1300px] table-fixed text-sm">
            <colgroup>
                <col style="width:45px">
                <col style="width:105px">
                <col style="width:170px">
                <col style="width:140px">
                <col style="width:65px">
                <col style="width:170px">
                <col style="width:170px">
                <col style="width:75px">
                <col style="width:115px">
                <col style="width:180px">
                @can('manage-complaints')
                    <col style="width:56px">
                @endcan
            </colgroup>
            <thead>
                <tr class="bg-slate-50/90 text-xs text-slate-500 uppercase tracking-wide border-b border-slate-200">
                    <th class="px-2 py-3 font-bold text-center whitespace-nowrap">No</th>
                    <th class="px-3 py-3 font-bold text-center whitespace-nowrap">Tgl Complain</th>
                    <th class="pl-6 pr-3 py-3 font-bold text-left whitespace-nowrap">Customer</th>
                    <th class="px-4 py-3 font-bold text-left whitespace-nowrap">Ukuran</th>
                    <th class="px-3 py-3 font-bold text-center whitespace-nowrap">Qty</th>
                    <th class="px-3 py-3 font-bold text-left whitespace-nowrap">Ketidaksesuaian</th>
                    <th class="px-3 py-3 font-bold text-left whitespace-nowrap">Penyebab</th>
                    <th class="px-3 py-3 font-bold text-center whitespace-nowrap">Lead</th>
                    <th class="px-3 py-3 font-bold text-center whitespace-nowrap">Status QA</th>
                    <th class="px-3 py-3 font-bold text-center whitespace-nowrap sticky {{ auth()->user()->can('manage-complaints') ? 'right-[56px]' : 'right-0' }} z-30 bg-slate-50 border-b border-slate-200 border-l border-slate-200 shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.06)]">Validasi SPV</th>
                    @can('manage-complaints')
                        <th class="px-2 py-3 font-bold text-center whitespace-nowrap sticky right-0 z-30 bg-slate-50 border-b border-slate-200 border-l border-slate-200/60">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($complaints as $index => $c)
                    @php
                        $ketTags    = $c->items->pluck('jenis_ketidaksesuaian')->filter()->unique()->values();
                        $detKetTags = $c->items->pluck('detail_ketidaksesuaian')->filter()->unique()->values();
                        $penTags    = $c->items->pluck('penyebab')->filter()->unique()->values();
                        $detPenTags = $c->items->pluck('detail_penyebab')->filter()->unique()->values();
                        $isOverdue  = $c->status === 'Open' && $c->tanggal_complain && \Carbon\Carbon::parse($c->tanggal_complain)->diffInDays(now()) > 7;
                        $isInvalidDates = $c->tanggal_complain && $c->tanggal_produksi && \Carbon\Carbon::parse($c->tanggal_complain)->lt(\Carbon\Carbon::parse($c->tanggal_produksi));
                        $approval   = $c->supervisor_approval; // null, Approved, Rejected, Pending
                        $stickyBg   = $isOverdue ? 'bg-rose-50 group-hover:bg-rose-100/80' : ($isInvalidDates ? 'bg-amber-50 group-hover:bg-amber-100/80' : 'bg-white group-hover:bg-slate-50');
                    @endphp
                    <tr class="group hover:bg-slate-50/80 transition-colors align-middle {{ $isOverdue ? 'bg-rose-50/50' : '' }} {{ $isInvalidDates ? 'bg-amber-50/40 border-l-4 border-l-amber-500' : '' }}">
                        <td class="px-2 py-3 text-xs font-semibold text-slate-500 text-center whitespace-nowrap">{{ $complaints->firstItem() ? ($complaints->firstItem() + $index) : ($index + 1) }}</td>
                        <td class="px-3 py-3 text-center whitespace-nowrap text-slate-700 text-xs font-medium">{{ optional($c->tanggal_complain)->format('d M Y') ?: '-' }}</td>
                        <td class="pl-6 pr-3 py-3 text-xs break-words">
                            <div class="font-semibold text-slate-800">{{ $c->nama_customer }}</div>
                            <div class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $c->no_customer }}</div>
                        </td>
                        <td class="px-4 py-3 text-left text-xs text-slate-700 font-medium leading-snug break-words">
                            {{ $c->ukuran ?: '-' }}
                        </td>
                        <td class="px-3 py-3 text-center whitespace-nowrap text-xs font-semibold text-slate-800">
                            {{ $c->qty ? number_format($c->qty) : '-' }}
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($ketTags as $k)
                                    <span class="text-xs font-medium bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-100 whitespace-nowrap">{{ $k }}</span>
                                @empty
                                    <span class="text-xs text-slate-300">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($penTags as $p)
                                    <span class="text-xs text-slate-700 font-medium">{{ $p }}{{ ! $loop->last ? ',' : '' }}</span>
                                @empty
                                    <span class="text-xs text-slate-300">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center text-xs whitespace-nowrap">
                            @if ($isInvalidDates)
                                <span class="text-rose-600 font-bold inline-flex items-center gap-1" title="Tanggal Complain mendahului Tanggal Produksi!">
                                    <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                    </svg>
                                    <span>{{ $c->lead_time }} hr</span>
                                </span>
                            @else
                                <span class="text-slate-600 font-medium">{{ $c->lead_time !== null ? $c->lead_time.' hr' : '-' }}</span>
                            @endif
                        </td>

                        {{-- STATUS QA COLUMN --}}
                        <td class="px-3 py-3 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center">
                                @if($c->status === 'Open')
                                    <span class="inline-flex items-center justify-center gap-1.5 text-xs h-7 px-2.5 rounded-md font-bold bg-amber-100 text-amber-800 border border-amber-300" @if($isOverdue) title="Kasus berumur lebih dari 7 hari" @endif>
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Open
                                    </span>
                                @elseif($c->status === 'Diproses')
                                    <span class="inline-flex items-center justify-center gap-1.5 text-xs h-7 px-2.5 rounded-md font-bold bg-sky-100 text-sky-800 border border-sky-300">
                                        <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span> Diproses
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center gap-1.5 text-xs h-7 px-2.5 rounded-md font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Close
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- VALIDASI SUPERVISOR / STATUS NCR COLUMN (Sticky) --}}
                        <td class="px-3 py-3 whitespace-nowrap text-center sticky {{ auth()->user()->can('manage-complaints') ? 'right-[56px]' : 'right-0' }} z-10 {{ $stickyBg }} border-l border-slate-200 shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.06)] transition-colors">
                            <div class="flex flex-col gap-1 items-center justify-center">
                                @if($c->status === 'Close')
                                    <a href="{{ route('complaints.ncr', $c) }}" target="_blank"
                                       class="btn btn-success btn-sm w-[150px] shadow-sm"
                                       title="Klik untuk melihat / mengunduh Surat NCR (PDF)">
                                        <i class="fas fa-file-pdf"></i>
                                        <span>NCR Terbit (PDF)</span>
                                    </a>
                                @elseif($c->status === 'Diproses')
                                    @can('approve-ncr')
                                        <button type="button"
                                            class="btn-validasi-spv btn btn-primary btn-sm w-[150px] shadow-sm cursor-pointer"
                                            title="Klik untuk membuka Form Validasi & Persetujuan NCR"
                                            data-id="{{ $c->id }}"
                                            data-no="{{ $c->no_customer }}"
                                            data-status="{{ $approval }}"
                                            data-catatan="{{ e($c->catatan_supervisor ?? '') }}"
                                            data-visit="{{ $c->perlu_visit ? 1 : 0 }}"
                                            data-tgl-visit="{{ optional($c->tanggal_visit)->format('Y-m-d') }}"
                                            data-jam-visit="{{ $c->jam_visit ? substr($c->jam_visit, 0, 5) : '' }}"
                                            data-catatan-visit="{{ e($c->catatan_visit ?? '') }}"
                                            data-customer="{{ e($c->nama_customer) }}"
                                            data-ket="{{ e(implode(', ', $ketTags->all())) }}"
                                            data-det-ket="{{ e(implode('; ', $detKetTags->all())) }}"
                                            data-pen="{{ e(implode(', ', $penTags->all())) }}"
                                            data-det-pen="{{ e(implode('; ', $detPenTags->all())) }}"
                                            data-corrective="{{ e($c->corrective_action ?? '') }}"
                                            data-preventive="{{ e($c->preventive_action ?? '') }}"
                                            data-deskripsi="{{ e($c->deskripsi_customer ?? '') }}"
                                            data-ukuran="{{ e($c->ukuran ?? '') }}"
                                            data-qty="{{ $c->qty ? number_format($c->qty) : '' }}"
                                            data-desk-pen="{{ e($c->deskripsi_penyebab ?? '') }}"
                                        >
                                            <i class="fas fa-check-circle"></i>
                                            <span>Validasi SPV</span>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1.5 px-3 rounded-md text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200 cursor-default select-none shadow-none"
                                              title="Menunggu Validasi CAPA dari Supervisor QC">
                                            <i class="fas fa-clock text-sky-500"></i>
                                            <span>Sedang Diajukan</span>
                                        </span>
                                    @endcan
                                @elseif($approval === 'Rejected')
                                    @can('manage-complaints')
                                        <a href="{{ route('complaints.edit', $c) }}"
                                           class="btn btn-danger btn-sm w-[150px] shadow-sm"
                                           title="Klik langsung untuk memperbarui investigasi 6M / CAPA">
                                            <i class="fas fa-edit"></i>
                                            <span>Perlu Revisi</span>
                                        </a>
                                    @else
                                        <button type="button"
                                            class="btn-validasi-spv btn btn-danger btn-sm w-[150px] shadow-sm cursor-pointer"
                                            title="Klik untuk meninjau ulang status revisi"
                                            data-id="{{ $c->id }}"
                                            data-no="{{ $c->no_customer }}"
                                            data-status="{{ $approval }}"
                                            data-catatan="{{ e($c->catatan_supervisor ?? '') }}"
                                            data-visit="{{ $c->perlu_visit ? 1 : 0 }}"
                                            data-tgl-visit="{{ optional($c->tanggal_visit)->format('Y-m-d') }}"
                                            data-jam-visit="{{ $c->jam_visit ? substr($c->jam_visit, 0, 5) : '' }}"
                                            data-catatan-visit="{{ e($c->catatan_visit ?? '') }}"
                                            data-customer="{{ e($c->nama_customer) }}"
                                            data-ket="{{ e(implode(', ', $ketTags->all())) }}"
                                            data-det-ket="{{ e(implode('; ', $detKetTags->all())) }}"
                                            data-pen="{{ e(implode(', ', $penTags->all())) }}"
                                            data-det-pen="{{ e(implode('; ', $detPenTags->all())) }}"
                                            data-corrective="{{ e($c->corrective_action ?? '') }}"
                                            data-preventive="{{ e($c->preventive_action ?? '') }}"
                                            data-deskripsi="{{ e($c->deskripsi_customer ?? '') }}"
                                            data-ukuran="{{ e($c->ukuran ?? '') }}"
                                            data-qty="{{ $c->qty ? number_format($c->qty) : '' }}"
                                            data-desk-pen="{{ e($c->deskripsi_penyebab ?? '') }}"
                                        >
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span>Status Revisi</span>
                                        </button>
                                    @endcan
                                @else
                                    @can('manage-complaints')
                                        <form action="{{ route('complaints.ajukan-validasi', $c) }}" method="POST" onsubmit="return confirm('Ajukan validasi CAPA kasus {{ $c->no_customer }} ke Supervisor QC?')" class="inline-block">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-warning btn-sm w-[150px] shadow-sm"
                                                    title="Klik langsung untuk mengajukan validasi ke Supervisor">
                                                <i class="fas fa-paper-plane"></i>
                                                <span>Ajukan Validasi</span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1.5 px-3 rounded-md text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-default select-none shadow-none"
                                              title="Kasus masih dalam proses investigasi oleh Staff QA dan belum diajukan">
                                            <i class="fas fa-file text-slate-300"></i>
                                            <span>Draft (Open)</span>
                                        </span>
                                    @endcan
                                @endif

                                @if($c->perlu_visit)
                                    <span class="inline-flex items-center justify-center gap-1 text-[11px] px-2 py-0.5 rounded font-semibold bg-purple-50 text-purple-700 border border-purple-200"
                                          title="{{ $c->catatan_visit ?: 'Kunjungan Lapangan SPV' }}">
                                        <svg class="w-3.5 h-3.5 shrink-0 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        <span>Visit: {{ optional($c->tanggal_visit)->format('d/m/Y') ?: 'Segera' }}</span>
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Actions Column: Only rendered for QA (Sticky) --}}
                        @can('manage-complaints')
                            <td class="px-2 py-3 text-center whitespace-nowrap sticky right-0 z-10 {{ $stickyBg }} border-l border-slate-200/60 transition-colors">
                                <div class="row-menu relative inline-block text-left">
                                    <button type="button" class="menu-btn p-1.5 rounded-md hover:bg-slate-100 text-slate-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zM10 11.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zM10 17a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                        </svg>
                                    </button>
                                    <div class="menu-panel hidden absolute right-0 top-full mt-1 w-52 bg-white border border-slate-200 rounded-lg shadow-xl z-50 py-1">
                                        <a href="{{ route('complaints.edit', $c) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                            </svg>
                                            <span>Edit / Investigasi QA</span>
                                        </a>
                                        @if($c->status !== 'Close' && $c->status !== 'Diproses')
                                            <form action="{{ route('complaints.ajukan-validasi', $c) }}" method="POST"
                                                  onsubmit="return confirm('Ajukan validasi CAPA kasus {{ $c->no_customer }} ke Supervisor QC?')">
                                                @csrf @method('PATCH')
                                                <button class="w-full flex items-center gap-2 text-left px-3 py-2 text-sm text-emerald-700 hover:bg-emerald-50">
                                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                                                    </svg>
                                                    <span>Ajukan Validasi</span>
                                                </button>
                                            </form>
                                        @endif
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <form action="{{ route('complaints.destroy', $c) }}" method="POST"
                                              onsubmit="return confirm('Hapus complaint {{ $c->no_customer }}?')">
                                            @csrf @method('DELETE')
                                            <button class="w-full flex items-center gap-2 text-left px-3 py-2 text-sm text-rose-600 hover:bg-rose-50">
                                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role === 'qa' ? 11 : 10 }}" class="px-4 py-12 text-center text-slate-400">
                            Tidak ada data complaint ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($complaints->total() > 0)
        <div class="p-4 border-t border-slate-100 bg-white">
            {{ $complaints->links('vendor.pagination.sb-admin-2', ['hidePerPage' => true]) }}
        </div>
    @endif
</div>

{{-- Modal Validasi CAPA & Persetujuan NCR Supervisor --}}
@can('approve-ncr')
<style>
    /* ===== SB Admin 2 — Modal Validasi SPV ===== */
    #approveModal { font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    #approveModal input, #approveModal textarea, #approveModal select, #approveModal button, #approveModal h5, #approveModal h6, #approveModal label {
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    /* Pastikan icon Font Awesome tidak ter-override font Nunito */
    #approveModal .fas, #approveModal .far, #approveModal .fa, #approveModal i[class*="fa-"] {
        font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", FontAwesome !important;
        font-weight: 900;
        font-style: normal;
        display: inline-block;
        line-height: 1;
    }

    #approveModal .sb-modal-content { background:#fff; border:1px solid rgba(0,0,0,.2); border-radius:.35rem; box-shadow:0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
    #approveModal .sb-modal-header { padding:1rem 1.25rem; border-bottom:1px solid #e3e6f0; display:flex; align-items:flex-start; justify-content:space-between; }
    #approveModal .sb-modal-title { margin:0; font-size:1.25rem; font-weight:700; line-height:1.5; color:#5a5c69; }
    #approveModal .sb-close { font-size:1.5rem; font-weight:700; line-height:1; color:#000; opacity:.5; background:transparent; border:0; padding:0 .25rem; cursor:pointer; }
    #approveModal .sb-close:hover { opacity:.75; }
    #approveModal .sb-modal-body { padding:1.25rem; }
    #approveModal .sb-modal-footer { padding:.75rem 1.25rem; border-top:1px solid #e3e6f0; display:flex; justify-content:flex-end; gap:.5rem; background:#f8f9fc; }

    #approveModal .sb-card { border:1px solid #e3e6f0; border-radius:.35rem; background:#fff; box-shadow:0 .15rem 1.75rem 0 rgba(58,59,69,.06); margin-bottom:1rem; }
    #approveModal .sb-card-header { padding:.75rem 1.25rem; background:#f8f9fc; border-bottom:1px solid #e3e6f0; border-radius:.35rem .35rem 0 0; display:flex; align-items:center; justify-content:space-between; gap:.5rem; }
    #approveModal .sb-card-header h6 { margin:0; font-size:.95rem; font-weight:700; color:#4e73df; }
    #approveModal .sb-card-body { padding:1.25rem; }

    #approveModal .sb-label-xs { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.03em; margin-bottom:.25rem; }
    #approveModal .text-primary-sb { color:#4e73df; } #approveModal .text-success-sb { color:#1cc88a; }
    #approveModal .text-info-sb { color:#36b9cc; }    #approveModal .text-warning-sb { color:#f6c23e; }
    #approveModal .text-danger-sb { color:#e74a3b; }  #approveModal .text-gray-800 { color:#5a5c69; }
    #approveModal .text-gray-600 { color:#858796; }   #approveModal .text-gray-500 { color:#b7b9cc; }
    #approveModal .sb-value { font-size:.95rem; font-weight:700; color:#5a5c69; }
    #approveModal .sb-text { font-size:.875rem; color:#858796; }

    #approveModal .sb-form-label { display:block; font-size:.8rem; font-weight:700; color:#5a5c69; margin-bottom:.35rem; }
    #approveModal .sb-form-control { display:block; width:100%; padding:.45rem .75rem; font-size:.85rem; line-height:1.5; color:#6e707e; background:#fff; border:1px solid #d1d3e2; border-radius:.35rem; transition:border-color .15s, box-shadow .15s; }
    #approveModal .sb-form-control:focus { outline:0; color:#6e707e; border-color:#bac8f3; box-shadow:0 0 0 .2rem rgba(78,115,223,.25); }
    #approveModal .sb-form-control::placeholder { color:#b7b9cc; }
    #approveModal .sb-help { font-size:80%; color:#858796; }

    /* Kolom Deactive / Disabled */
    #approveModal .sb-disabled, #approveModal .sb-form-control:disabled, #approveModal .sb-form-control[readonly] {
        background-color: #eaecf4 !important;
        border-color: #d1d3e2 !important;
        color: #5a5c69 !important;
        cursor: not-allowed;
        opacity: 1;
    }

    #approveModal .sb-decision { text-align:left; border:1px solid #e3e6f0; border-left:.25rem solid #e3e6f0; border-radius:.35rem; padding:.85rem 1rem; background:#fff; transition:all .15s; cursor:pointer; width:100%; }
    #approveModal .sb-decision:hover { box-shadow:0 .125rem .25rem 0 rgba(58,59,69,.2); }
    #approveModal .sb-decision .sb-decision-icon { width:2rem; height:2rem; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; background:#eaecf4; color:#b7b9cc; font-size:.8rem; }
    #approveModal .sb-decision[data-decision="Approved"].active { border-left-color:#1cc88a; background:#f3fcf8; }
    #approveModal .sb-decision[data-decision="Approved"].active .sb-decision-icon { background:#1cc88a; color:#fff; }
    #approveModal .sb-decision[data-decision="Pending"].active  { border-left-color:#f6c23e; background:#fffbf0; }
    #approveModal .sb-decision[data-decision="Pending"].active .sb-decision-icon  { background:#f6c23e; color:#fff; }
    #approveModal .sb-decision[data-decision="Rejected"].active { border-left-color:#e74a3b; background:#fdf3f2; }
    #approveModal .sb-decision[data-decision="Rejected"].active .sb-decision-icon { background:#e74a3b; color:#fff; }

    #approveModal .sb-radio { display:flex; align-items:flex-start; gap:.6rem; padding:.75rem 1rem; border:1px solid #e3e6f0; border-radius:.35rem; background:#fff; cursor:pointer; transition:all .15s; }
    #approveModal .sb-radio:has(input:checked) { border-color:#4e73df; background:#f0f3fd; }

    #approveModal .sb-switch { position:relative; display:inline-block; width:2.25rem; height:1.25rem; flex-shrink:0; }
    #approveModal .sb-switch input { opacity:0; width:0; height:0; }
    #approveModal .sb-switch .slider { position:absolute; inset:0; background:#d1d3e2; border-radius:1rem; transition:.15s; cursor:pointer; }
    #approveModal .sb-switch .slider:before { content:""; position:absolute; width:.9rem; height:.9rem; left:.175rem; top:.175rem; background:#fff; border-radius:50%; transition:.15s; }
    #approveModal .sb-switch input:checked + .slider { background:#4e73df; }
    #approveModal .sb-switch input:checked + .slider:before { transform:translateX(1rem); }
</style>

<div id="approveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.5); backdrop-filter: blur(2px);">
    <div class="sb-modal-content w-full max-w-2xl max-h-[92vh] flex flex-col overflow-hidden">

        {{-- Modal Header --}}
        <div class="sb-modal-header shrink-0">
            <div>
                <h5 class="sb-modal-title flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-primary-sb"></i>
                    <span>Validasi CAPA &amp; Persetujuan NCR</span>
                </h5>
                <div class="sb-text mt-1">
                    No. NCR <span class="font-bold text-primary-sb" id="approveNoCustomerBadge">-</span>
                    <span class="text-gray-500 mx-1.5">|</span>
                    Customer <span class="font-bold text-gray-800" id="approveCustomerName">-</span>
                </div>
            </div>
            <button type="button" class="sb-close" aria-label="Close"
                    onclick="document.getElementById('approveModal').classList.add('hidden')">&times;</button>
        </div>

        <form id="approveForm" method="POST" action="" class="flex flex-col flex-1 min-h-0">
            @csrf
            @method('PATCH')

            {{-- Modal Body --}}
            <div class="sb-modal-body flex-1 overflow-y-auto" style="background:#f8f9fc">

                {{-- Card 1: Ringkasan Kasus (Semua kolom deactive/readonly) --}}
                <div class="sb-card">
                    <div class="sb-card-header">
                        <h6><i class="fas fa-info-circle mr-1.5"></i> Ringkasan Kasus</h6>
                        <span class="badge badge-light" id="approveSpecsQty">-</span>
                    </div>
                    <div class="sb-card-body">
                        {{-- Kolom Deactive: Ketidaksesuaian & Penyebab --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="sb-form-label">Jenis Ketidaksesuaian</label>
                                <input type="text" id="approveKetidaksesuaian" readonly disabled class="sb-form-control sb-disabled font-bold text-gray-800" value="-">
                                <label class="sb-form-label mt-2">Detail Ketidaksesuaian</label>
                                <input type="text" id="approveDetailKetidaksesuaian" readonly disabled class="sb-form-control sb-disabled" value="-">
                            </div>
                            <div>
                                <label class="sb-form-label">Penyebab Teridentifikasi</label>
                                <input type="text" id="approvePenyebab" readonly disabled class="sb-form-control sb-disabled font-bold text-gray-800" value="-">
                                <label class="sb-form-label mt-2">Detail Penyebab</label>
                                <input type="text" id="approveDetailPenyebab" readonly disabled class="sb-form-control sb-disabled" value="-">
                            </div>
                        </div>

                        {{-- Kolom Deactive: Keluhan Customer --}}
                        <div class="mb-3">
                            <label class="sb-form-label">Keluhan Customer</label>
                            <textarea id="approveDeskripsi" readonly disabled rows="2" class="sb-form-control sb-disabled"></textarea>
                        </div>

                        {{-- Kolom Deactive: Narasi Penyebab (dicetak di NCR) --}}
                        <div>
                            <label class="sb-form-label">Narasi Penyebab <span class="text-xs text-gray-500 font-normal">(dicetak di Surat NCR)</span></label>
                            <textarea id="approveDeskripsiPenyebab" readonly disabled rows="2" class="sb-form-control sb-disabled"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Tinjau Tindakan CAPA --}}
                <div class="sb-card">
                    <div class="sb-card-header">
                        <h6><i class="fas fa-tools mr-1.5"></i> Tinjau Tindakan CAPA</h6>
                        <span class="badge badge-primary">Tugas SPV</span>
                    </div>
                    <div class="sb-card-body">
                        <div class="mb-3">
                            <label for="approveCorrectiveInput" class="sb-form-label">Corrective Action</label>
                            <textarea name="corrective_action" id="approveCorrectiveInput" rows="3"
                                      placeholder="Belum ada usulan tindakan perbaikan dari QA"
                                      class="sb-form-control"></textarea>
                            <small class="sb-help">Tindakan penanganan cacat saat ini. Dapat disunting oleh SPV.</small>
                        </div>
                        <div>
                            <label for="approvePreventiveInput" class="sb-form-label">Preventive Action</label>
                            <textarea name="preventive_action" id="approvePreventiveInput" rows="3"
                                      placeholder="Belum ada usulan tindakan pencegahan dari QA"
                                      class="sb-form-control"></textarea>
                            <small class="sb-help">Tindakan pencegahan agar masalah tidak terulang.</small>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Keputusan --}}
                <div class="sb-card">
                    <div class="sb-card-header">
                        <h6><i class="fas fa-gavel mr-1.5"></i> Keputusan Validasi <span class="text-danger-sb">*</span></h6>
                    </div>
                    <div class="sb-card-body">
                        {{-- Select asli tetap dipakai sistem (disembunyikan secara visual) --}}
                        <select name="approval_status" id="approveStatusSelect" class="sr-only" tabindex="-1" aria-hidden="true"
                                onchange="document.getElementById('keteranganBox').classList.toggle('hidden', this.value !== 'Approved'); syncDecisionUI();">
                            <option value="Approved">Approved</option>
                            <option value="Pending">Pending</option>
                            <option value="Rejected">Rejected</option>
                        </select>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2" id="decisionCards">
                            <button type="button" data-decision="Approved" onclick="setDecision('Approved')" class="decision-card sb-decision">
                                <div class="flex items-center gap-2">
                                    <span class="sb-decision-icon"><i class="fas fa-check"></i></span>
                                    <span class="font-bold text-gray-800" style="font-size:.9rem">Setujui</span>
                                </div>
                                <div class="sb-help mt-1">CAPA sah, kasus Close &amp; NCR terbit</div>
                            </button>
                            <button type="button" data-decision="Pending" onclick="setDecision('Pending')" class="decision-card sb-decision">
                                <div class="flex items-center gap-2">
                                    <span class="sb-decision-icon"><i class="fas fa-pause"></i></span>
                                    <span class="font-bold text-gray-800" style="font-size:.9rem">Pending</span>
                                </div>
                                <div class="sb-help mt-1">Butuh peninjauan / data tambahan</div>
                            </button>
                            <button type="button" data-decision="Rejected" onclick="setDecision('Rejected')" class="decision-card sb-decision">
                                <div class="flex items-center gap-2">
                                    <span class="sb-decision-icon"><i class="fas fa-undo"></i></span>
                                    <span class="font-bold text-gray-800" style="font-size:.9rem">Revisi</span>
                                </div>
                                <div class="sb-help mt-1">Kembalikan ke QA untuk diperbaiki</div>
                            </button>
                        </div>

                        {{-- Keterangan Penyelesaian (hanya saat Approved) --}}
                        <div id="keteranganBox" class="mt-3">
                            <label class="sb-form-label">Bentuk Penyelesaian ke Customer</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="sb-radio">
                                    <input type="radio" name="keterangan" value="Feedback" class="mt-1" style="accent-color:#4e73df" checked>
                                    <div>
                                        <div class="font-bold text-gray-800" style="font-size:.875rem">Feedback</div>
                                        <div class="sb-help">Klarifikasi tanpa pengembalian barang</div>
                                    </div>
                                </label>
                                <label class="sb-radio">
                                    <input type="radio" name="keterangan" value="Retur" class="mt-1" style="accent-color:#4e73df">
                                    <div>
                                        <div class="font-bold text-gray-800" style="font-size:.875rem">Retur</div>
                                        <div class="sb-help">Barang dikembalikan untuk penggantian</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Catatan SPV --}}
                        <div class="mt-3">
                            <label for="approveCatatan" class="sb-form-label flex justify-between items-baseline">
                                <span>Catatan untuk QA</span>
                                <span class="sb-help font-normal" id="catatanHint">Opsional</span>
                            </label>
                            <textarea name="catatan_supervisor" id="approveCatatan" rows="2"
                                      placeholder="Tulis arahan atau poin yang perlu diperbaiki..."
                                      class="sb-form-control"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Visit Customer --}}
                <div class="sb-card" style="margin-bottom:0">
                    <div class="sb-card-header">
                        <h6><i class="fas fa-map-marker-alt mr-1.5"></i> Kunjungan Customer</h6>
                        <label class="flex items-center gap-2 cursor-pointer sb-text">
                            <span>Jadwalkan</span>
                            <span class="sb-switch">
                                <input type="checkbox" name="perlu_visit" id="approvePerluVisit" value="1" onchange="toggleVisitFields(this.checked)">
                                <span class="slider"></span>
                            </span>
                        </label>
                    </div>
                    <div id="visitFieldsBox" class="hidden sb-card-body">
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label for="approveTanggalVisit" class="sb-form-label">Tanggal</label>
                                <input type="date" name="tanggal_visit" id="approveTanggalVisit" min="{{ date('Y-m-d') }}" class="sb-form-control">
                            </div>
                            <div>
                                <label for="approveJamVisit" class="sb-form-label">Jam</label>
                                <input type="time" name="jam_visit" id="approveJamVisit" class="sb-form-control">
                            </div>
                        </div>
                        <div>
                            <label for="approveCatatanVisit" class="sb-form-label">Tujuan Kunjungan</label>
                            <input type="text" name="catatan_visit" id="approveCatatanVisit" placeholder="Misal: cek fisik sampel di lokasi customer" class="sb-form-control">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="sb-modal-footer shrink-0">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('approveModal').classList.add('hidden')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Validasi CAPA &amp; NCR</button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- Import Modal --}}
@can('manage-complaints')
<div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-[#f6c23e] text-slate-800 flex items-center justify-between font-bold">
            <h3 class="text-base font-bold flex items-center gap-2">
                <i class="fas fa-file-import"></i>
                <span>Import Data Complaint Excel</span>
            </h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-600 hover:text-slate-900 font-bold text-lg">&times;</button>
        </div>
        <form action="{{ route('complaints.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih File Excel (.xlsx / .xls)</label>
                <input type="file" name="file" required accept=".xlsx,.xls" class="w-full text-sm border border-slate-300 rounded-lg p-2">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-warning btn-sm flex-1 py-2.5 text-white">
                    <i class="fas fa-file-import"></i>
                    <span>Upload &amp; Import</span>
                </button>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="btn btn-light btn-sm flex-1 py-2.5 border border-slate-200">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script>
// --- Dropdown Menu Aksi dengan Penanganan Stacking Context & Auto Up/Down ---
function closeAllRowMenus() {
    document.querySelectorAll('.menu-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('tr.menu-active').forEach(r => {
        r.classList.remove('menu-active');
        r.style.zIndex = '';
    });
    document.querySelectorAll('td.menu-active').forEach(c => {
        c.classList.remove('menu-active');
        c.style.zIndex = '';
    });
}

document.querySelectorAll('.row-menu').forEach(menu => {
    const btn = menu.querySelector('.menu-btn');
    const panel = menu.querySelector('.menu-panel');
    const tr = menu.closest('tr');
    const td = menu.closest('td');

    btn?.addEventListener('click', (e) => {
        e.stopPropagation();
        const willOpen = panel.classList.contains('hidden');
        closeAllRowMenus();

        if (willOpen) {
            panel.classList.remove('hidden');
            if (tr) {
                tr.classList.add('menu-active');
                tr.style.zIndex = '40';
            }
            if (td) {
                td.classList.add('menu-active');
                td.style.zIndex = '40';
            }

            // Cek apakah posisi dekat bagian bawah layar / container agar popup otomatis membuka ke atas
            const rect = btn.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            if (spaceBelow < 180) {
                panel.style.top = 'auto';
                panel.style.bottom = '100%';
                panel.style.marginBottom = '4px';
                panel.style.marginTop = '0';
            } else {
                panel.style.top = '100%';
                panel.style.bottom = 'auto';
                panel.style.marginTop = '4px';
                panel.style.marginBottom = '0';
            }
        }
    });
});

document.addEventListener('click', () => {
    closeAllRowMenus();
});

function toggleVisitFields(checked) {
    const box = document.getElementById('visitFieldsBox');
    if (box) {
        if (checked) box.classList.remove('hidden');
        else box.classList.add('hidden');
    }
}

// --- Tampilan kartu keputusan (hanya UI; nilai tetap dari select #approveStatusSelect) ---
function setDecision(value) {
    const sel = document.getElementById('approveStatusSelect');
    if (!sel) return;
    sel.value = value;
    sel.dispatchEvent(new Event('change'));
}
function syncDecisionUI() {
    const sel = document.getElementById('approveStatusSelect');
    if (!sel) return;
    document.querySelectorAll('#decisionCards .decision-card').forEach(card => {
        card.classList.toggle('active', card.dataset.decision === sel.value);
    });
    const hint = document.getElementById('catatanHint');
    if (hint) {
        const needNote = sel.value !== 'Approved';
        hint.textContent = needNote ? 'Disarankan diisi' : 'Opsional';
        hint.style.color = needNote ? '#e74a3b' : '#858796';
        hint.style.fontWeight = needNote ? '700' : '400';
    }
}

// Attach click listener ke semua tombol validasi SPV (pakai data-* attribute agar aman dari karakter khusus)
document.querySelectorAll('.btn-validasi-spv').forEach(btn => {
    btn.addEventListener('click', function () {
        const d = this.dataset;
        openApproveModal(
            d.id, d.no, d.status, d.catatan,
            parseInt(d.visit), d.tglVisit, d.jamVisit, d.catatanVisit,
            d.customer, d.ket, d.detKet, d.pen, d.detPen,
            d.corrective, d.preventive, d.deskripsi,
            d.ukuran, d.qty, d.deskPen
        );
    });
});

function openApproveModal(id, noCust, currentStatus, currentCatatan, perluVisit, tanggalVisit, jamVisit, catatanVisit, customerName, ketidaksesuaian, detailKetidaksesuaian, penyebab, detailPenyebab, corrective, preventive, deskripsi, ukuran, qty, deskripsiPenyebab) {
    const form = document.getElementById('approveForm');
    form.action = `/complaints/${id}/approve`;

    const setVal = (elemId, val) => {
        const el = document.getElementById(elemId);
        if (!el) return;
        if ('value' in el) el.value = val;
        el.innerText = val;
    };

    document.getElementById('approveCustomerName').innerText = customerName || '-';
    document.getElementById('approveNoCustomerBadge').innerText = noCust || '-';
    setVal('approveKetidaksesuaian', ketidaksesuaian || '-');
    setVal('approveDetailKetidaksesuaian', detailKetidaksesuaian || '-');
    setVal('approvePenyebab', penyebab || '-');
    setVal('approveDetailPenyebab', detailPenyebab || '-');

    setVal('approveDeskripsi', (deskripsi && deskripsi.trim() !== '') ? deskripsi : 'Tidak ada deskripsi rinci dari customer/QA.');
    setVal('approveDeskripsiPenyebab', (deskripsiPenyebab && deskripsiPenyebab.trim() !== '')
        ? deskripsiPenyebab
        : (penyebab || 'Tidak ada deskripsi narasi penyebab.'));

    let specs = '';
    if (ukuran) specs += 'Ukuran: ' + ukuran;
    if (qty) specs += (specs ? ' | ' : '') + 'Qty: ' + qty + ' pcs';
    document.getElementById('approveSpecsQty').innerText = specs || 'Rincian: -';

    document.getElementById('approveCorrectiveInput').value = corrective || '';
    document.getElementById('approvePreventiveInput').value = preventive || '';

    const statusSelect = document.getElementById('approveStatusSelect');
    statusSelect.value = (currentStatus && currentStatus !== 'null') ? currentStatus : 'Approved';
    const ketBox = document.getElementById('keteranganBox');
    if (ketBox) {
        ketBox.classList.toggle('hidden', statusSelect.value !== 'Approved');
    }
    syncDecisionUI();
    document.getElementById('approveCatatan').value = currentCatatan;

    const chk = document.getElementById('approvePerluVisit');
    chk.checked = !!perluVisit;
    toggleVisitFields(!!perluVisit);

    const tglEl = document.getElementById('approveTanggalVisit');
    if (tglEl) tglEl.value = tanggalVisit || '';
    const jamEl = document.getElementById('approveJamVisit');
    if (jamEl) jamEl.value = jamVisit || '';
    const catEl = document.getElementById('approveCatatanVisit');
    if (catEl) catEl.value = catatanVisit || '';
    
    // Reset the submit button state in case it was disabled previously
    if (form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Simpan Validasi CAPA &amp; NCR';
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    }

    document.getElementById('approveModal').classList.remove('hidden');
}

// Prevent double submission and show loading state
document.getElementById('approveForm')?.addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 inline-block mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyimpan...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    }
});
</script>
@endpush
