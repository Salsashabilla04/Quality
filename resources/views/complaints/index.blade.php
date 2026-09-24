@extends('layouts.app')
@section('title', 'Data Complaint & NCR')
@section('subtitle', 'Monitoring, investigasi QA, dan persetujuan (approval) Supervisor QC')

@section('actions')
    <div class="flex items-center gap-2">

        @can('manage-complaints')
            <button
                type="button"
                onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="inline-flex items-center justify-center gap-2
                       h-10 px-4
                       rounded-xl
                       bg-amber-500 hover:bg-amber-600
                       text-sm font-semibold text-white
                       shadow-sm
                       transition-all duration-200
                       shrink-0
                       whitespace-nowrap"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"
                    />
                </svg>

                <span>Import Excel</span>
            </button>
        @endcan


        @can('manage-complaints')
            <a
                href="{{ route('export.complaints.excel', request()->query()) }}"
                class="inline-flex items-center justify-center gap-2
                       h-10 px-4
                       rounded-xl
                       bg-emerald-600 hover:bg-emerald-700
                       text-sm font-semibold text-white
                       shadow-sm
                       transition-all duration-200
                       shrink-0
                       whitespace-nowrap"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 21V9m0 0 4 4m-4-4-4 4M5 3h14"
                    />
                </svg>

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
    <div class="mb-5 bg-rose-50 border-2 border-rose-300 rounded-xl p-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-rose-600 text-white flex items-center justify-center font-extrabold text-xl shrink-0">!</div>
            <div>
                <div class="text-sm font-bold text-rose-900">PERHATIAN QA: {{ $revisiCount }} Dokumen NCR Membutuhkan Perbaikan / Revisi dari Supervisor!</div>
                <div class="text-xs text-rose-700 mt-0.5">Supervisor QC meminta perbaikan pada analisis atau tindakan perbaikan. Klik tombol di kanan untuk memfilter dan memperbaiki kasus.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['revisi' => 1]) }}" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg shrink-0 shadow-sm">Lihat Kasus Perlu Revisi ({{ $revisiCount }})</a>
    </div>
    @endif
@endcan

@can('approve-ncr')
    @if($pendingSpvCount > 0)
    <div class="mb-5 bg-amber-50 border-2 border-amber-300 rounded-xl p-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center font-extrabold text-xl shrink-0">⏳</div>
            <div>
                <div class="text-sm font-bold text-amber-900">MEMBUTUHKAN VALIDASI: {{ $pendingSpvCount }} Dokumen NCR Menunggu Persetujuan Anda!</div>
                <div class="text-xs text-amber-700 mt-0.5">Silakan tinjau data komplain dan klik tombol "🛡️ Validasi SPV" pada baris komplain di bawah.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg shrink-0 shadow-sm">Lihat Kasus Pending Approval ({{ $pendingSpvCount }})</a>
    </div>
    @endif
@endcan

<form method="GET" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-5 flex flex-col gap-4">
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[250px]">
            <label class="block text-xs font-medium text-slate-600 mb-1">Cari</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik nama customer, jenis defect, detail, atau penyebab..."
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
        </div>
        
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Status QA</label>
            <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500 w-36">
                <option value="">Semua Status</option>
                <option value="Open" @selected(request('status')==='Open')>Open (Baru)</option>
                <option value="Diproses" @selected(request('status')==='Diproses')>Diproses QA</option>
                <option value="Close" @selected(request('status')==='Close')>Close (Selesai)</option>
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Tahun</label>
            <select name="tahun" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500 w-28">
                <option value="">Semua</option>
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected(request()->integer('tahun') === (int) $y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Bulan</label>
            <select name="bulan" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500 w-32">
                <option value="">Semua</option>
                @foreach ($namaBulan as $n => $label)
                    <option value="{{ $n }}" @selected(request()->integer('bulan') === $n)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="flex flex-wrap gap-4 items-center pt-2 border-t border-slate-100">
        <label class="inline-flex items-center gap-2 cursor-pointer bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-lg hover:bg-rose-100 transition-colors">
            <input type="checkbox" name="anomali" value="1" @checked(request('anomali')) class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
            <span class="text-xs font-semibold text-rose-700">⚠️ Filter Anomali Lead Time</span>
        </label>
        
        <div class="flex-1"></div>

        @if (request()->hasAny(['q','defect','detail_defect','status','tahun','bulan','anomali']))
            <a href="{{ route('complaints.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 px-3 py-2">Reset</a>
        @endif
        <button class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-sm px-6 py-2 rounded-lg transition-colors">Terapkan Filter</button>
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    <div class="overflow-x-auto min-h-[320px] pb-12">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase tracking-wide border-b border-slate-200">
                    <th class="px-3.5 py-3 font-bold whitespace-nowrap">No</th>
                    <th class="px-3.5 py-3 font-bold whitespace-nowrap">Tgl Complain</th>
                    <th class="px-3.5 py-3 font-bold whitespace-nowrap">Customer</th>
                    <th class="px-3.5 py-3 font-bold whitespace-nowrap">Ukuran</th>
                    <th class="px-3.5 py-3 font-bold text-right whitespace-nowrap">Qty</th>
                    <th class="px-3.5 py-3 font-bold whitespace-nowrap">Ketidaksesuaian</th>
                    <th class="px-3.5 py-3 font-bold whitespace-nowrap">Penyebab</th>
                    <th class="px-3.5 py-3 font-bold text-right whitespace-nowrap">Lead</th>
                    <th class="px-3.5 py-3 font-bold whitespace-nowrap">Status QA</th>
                    <th class="px-3.5 py-3 font-bold whitespace-nowrap">Validasi SPV</th>
                    @can('manage-complaints')
                        <th class="px-3.5 py-3 font-bold text-right whitespace-nowrap">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($complaints as $c)
                    @php
                        $ketTags    = $c->items->pluck('jenis_ketidaksesuaian')->filter()->unique()->values();
                        $detKetTags = $c->items->pluck('detail_ketidaksesuaian')->filter()->unique()->values();
                        $penTags    = $c->items->pluck('penyebab')->filter()->unique()->values();
                        $detPenTags = $c->items->pluck('detail_penyebab')->filter()->unique()->values();
                        $isOverdue  = $c->status === 'Open' && $c->tanggal_complain && \Carbon\Carbon::parse($c->tanggal_complain)->diffInDays(now()) > 7;
                        $isInvalidDates = $c->tanggal_complain && $c->tanggal_produksi && \Carbon\Carbon::parse($c->tanggal_complain)->lt(\Carbon\Carbon::parse($c->tanggal_produksi));
                        $approval   = $c->supervisor_approval; // null, Approved, Rejected, Pending
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors align-middle {{ $isOverdue ? 'bg-rose-50/50' : '' }} {{ $isInvalidDates ? 'bg-amber-50/40 border-l-4 border-l-amber-500' : '' }}">
                        <td class="px-3.5 py-3 font-mono text-xs font-semibold text-slate-600 whitespace-nowrap">{{ $c->no_customer }}</td>
                        <td class="px-3.5 py-3 whitespace-nowrap text-slate-700 text-xs font-medium">{{ optional($c->tanggal_complain)->format('d M Y') }}</td>
                        <td class="px-3.5 py-3 font-bold text-slate-800 whitespace-nowrap">{{ $c->nama_customer }}</td>
                        <td class="px-3.5 py-3 text-xs text-slate-500 whitespace-nowrap">{{ $c->ukuran ?: '-' }}</td>
                        <td class="px-3.5 py-3 text-right font-medium text-slate-700 whitespace-nowrap">{{ $c->qty ? number_format($c->qty) : '-' }}</td>
                        <td class="px-3.5 py-3">
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                @forelse ($ketTags as $k)
                                    <span class="text-xs font-medium bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-100 whitespace-nowrap">{{ $k }}</span>
                                @empty
                                    <span class="text-xs text-slate-300">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-3.5 py-3">
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                @forelse ($penTags as $p)
                                    <span class="text-xs text-slate-600 font-medium">{{ $p }}{{ ! $loop->last ? ',' : '' }}</span>
                                @empty
                                    <span class="text-xs text-slate-300">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-3.5 py-3 text-right text-xs whitespace-nowrap">
                            @if ($isInvalidDates)
                                <span class="text-rose-600 font-bold inline-flex items-center gap-1" title="Tanggal Complain mendahului Tanggal Produksi!">
                                    ⚠️ {{ $c->lead_time }} hr
                                </span>
                            @else
                                <span class="text-slate-500 font-medium">{{ $c->lead_time !== null ? $c->lead_time.' hr' : '-' }}</span>
                            @endif
                        </td>

                        {{-- STATUS QA COLUMN --}}
                        <td class="px-3.5 py-3 whitespace-nowrap">
                            @if($c->status === 'Open')
                                <span class="inline-flex items-center justify-center gap-1.5 text-xs h-8 min-w-[90px] px-2.5 rounded-md font-bold bg-amber-100 text-amber-800 border border-amber-300" @if($isOverdue) title="Kasus berumur lebih dari 7 hari" @endif>
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Open
                                </span>
                            @elseif($c->status === 'Diproses')
                                <span class="inline-flex items-center justify-center gap-1.5 text-xs h-8 min-w-[90px] px-2.5 rounded-md font-bold bg-sky-100 text-sky-800 border border-sky-300">
                                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span> Diproses
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center gap-1.5 text-xs h-8 min-w-[90px] px-2.5 rounded-md font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Close
                                </span>
                            @endif
                        </td>

                        {{-- VALIDASI SUPERVISOR / STATUS NCR COLUMN --}}
                        <td class="px-3.5 py-3 whitespace-nowrap">
                            <div class="flex flex-col gap-1 items-start">
                                @if($c->status === 'Close')
                                    <a href="{{ route('complaints.ncr', $c) }}" target="_blank"
                                       class="inline-flex items-center justify-center gap-1.5 text-xs h-8 w-[150px] rounded-lg font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all hover:scale-[1.02] active:scale-[0.98]"
                                       title="Klik untuk melihat / mengunduh Surat NCR (PDF)">
                                        <span>📄</span>
                                        <span>NCR Terbit (PDF)</span>
                                    </a>
                                @elseif($c->status === 'Diproses')
                                    @can('approve-ncr')
                                        <button type="button"
                                            onclick="openApproveModal({{ $c->id }}, '{{ $c->no_customer }}', '{{ $approval }}', '{{ addslashes($c->catatan_supervisor ?? '') }}', {{ $c->perlu_visit ? 1 : 0 }}, '{{ optional($c->tanggal_visit)->format('Y-m-d') }}', '{{ $c->jam_visit ? substr($c->jam_visit, 0, 5) : '' }}', '{{ addslashes($c->catatan_visit ?? '') }}', '{{ addslashes($c->nama_customer) }}', '{{ addslashes(implode(', ', $ketTags->all())) }}', '{{ addslashes(implode('; ', $detKetTags->all())) }}', '{{ addslashes(implode(', ', $penTags->all())) }}', '{{ addslashes(implode('; ', $detPenTags->all())) }}', '{{ addslashes($c->corrective_action ?? '') }}', '{{ addslashes($c->preventive_action ?? '') }}', '{{ addslashes($c->deskripsi_customer ?? '') }}', '{{ addslashes($c->ukuran ?? '') }}', '{{ $c->qty ? number_format($c->qty) : '' }}', '{{ addslashes($c->deskripsi_penyebab ?? '') }}')"
                                            class="inline-flex items-center justify-center gap-1.5 text-xs h-8 w-[150px] rounded-lg font-bold bg-violet-600 hover:bg-violet-700 text-white shadow-sm transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                                            title="Klik untuk membuka Form Validasi & Persetujuan NCR">
                                            <span>🛡️</span>
                                            <span>Validasi SPV</span>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center justify-center gap-1.5 text-xs h-8 w-[150px] rounded-lg font-bold bg-sky-100 text-sky-800 border border-sky-300 shadow-2xs"
                                              title="Menunggu Validasi CAPA dari Supervisor QC">
                                            <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0 animate-pulse"></span>
                                            <span>Sedang Diajukan</span>
                                        </span>
                                    @endcan
                                @elseif($approval === 'Rejected')
                                    @can('manage-complaints')
                                        <a href="{{ route('complaints.edit', $c) }}"
                                           class="inline-flex items-center justify-center gap-1.5 text-xs h-8 w-[150px] rounded-lg font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-all hover:scale-[1.02] active:scale-[0.98]"
                                           title="Klik langsung untuk memperbarui investigasi 6M / CAPA">
                                            <span>⚠️</span>
                                            <span>Perlu Revisi</span>
                                        </a>
                                    @else
                                        <button type="button"
                                            onclick="openApproveModal({{ $c->id }}, '{{ $c->no_customer }}', '{{ $approval }}', '{{ addslashes($c->catatan_supervisor ?? '') }}', {{ $c->perlu_visit ? 1 : 0 }}, '{{ optional($c->tanggal_visit)->format('Y-m-d') }}', '{{ $c->jam_visit ? substr($c->jam_visit, 0, 5) : '' }}', '{{ addslashes($c->catatan_visit ?? '') }}', '{{ addslashes($c->nama_customer) }}', '{{ addslashes(implode(', ', $ketTags->all())) }}', '{{ addslashes(implode('; ', $detKetTags->all())) }}', '{{ addslashes(implode(', ', $penTags->all())) }}', '{{ addslashes(implode('; ', $detPenTags->all())) }}', '{{ addslashes($c->corrective_action ?? '') }}', '{{ addslashes($c->preventive_action ?? '') }}', '{{ addslashes($c->deskripsi_customer ?? '') }}', '{{ addslashes($c->ukuran ?? '') }}', '{{ $c->qty ? number_format($c->qty) : '' }}', '{{ addslashes($c->deskripsi_penyebab ?? '') }}')"
                                            class="inline-flex items-center justify-center gap-1.5 text-xs h-8 w-[150px] rounded-lg font-bold bg-rose-100 hover:bg-rose-200 text-rose-800 border border-rose-300 transition-all cursor-pointer"
                                            title="Klik untuk meninjau ulang status revisi">
                                            <span>⚠️</span>
                                            <span>Status Revisi</span>
                                        </button>
                                    @endcan
                                @else
                                    @can('manage-complaints')
                                        <form action="{{ route('complaints.ajukan-validasi', $c) }}" method="POST" onsubmit="return confirm('Ajukan validasi CAPA kasus {{ $c->no_customer }} ke Supervisor QC?')" class="inline-block">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 text-xs h-8 w-[150px] rounded-lg font-bold bg-orange-500 hover:bg-orange-600 text-white shadow-xs transition-all hover:scale-[1.02] active:scale-[0.98]"
                                                    title="Klik langsung untuk mengajukan validasi ke Supervisor">
                                                <span>🚀</span>
                                                <span>Ajukan Validasi</span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center justify-center gap-1.5 text-xs h-8 w-[150px] rounded-lg font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span>Draft (Open)</span>
                                        </span>
                                    @endcan
                                @endif

                                @if($c->perlu_visit)
                                    <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded font-semibold bg-purple-50 text-purple-700 border border-purple-200"
                                          title="{{ $c->catatan_visit ?: 'Kunjungan Lapangan SPV' }}">
                                        🚐 Visit: {{ optional($c->tanggal_visit)->format('d/m/Y') ?: 'Segera' }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Actions Column: Only rendered for QA --}}
                        @can('manage-complaints')
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="row-menu relative inline-block text-left">
                                    <button type="button" class="menu-btn p-1.5 rounded-lg hover:bg-slate-100 text-slate-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zM10 11.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zM10 17a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                        </svg>
                                    </button>
                                    <div class="menu-panel hidden absolute right-0 mt-1 w-52 bg-white border border-slate-200 rounded-lg shadow-xl z-30 py-1">
                                        <a href="{{ route('complaints.edit', $c) }}" class="block px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">✏️ Edit / Investigasi QA</a>
                                        @if($c->status !== 'Close' && $c->status !== 'Diproses')
                                            <form action="{{ route('complaints.ajukan-validasi', $c) }}" method="POST"
                                                  onsubmit="return confirm('Ajukan validasi CAPA kasus {{ $c->no_customer }} ke Supervisor QC?')">
                                                @csrf @method('PATCH')
                                                <button class="w-full text-left px-3 py-2 text-sm text-emerald-700 hover:bg-emerald-50">🚀 Ajukan Validasi</button>
                                            </form>
                                        @endif
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <form action="{{ route('complaints.destroy', $c) }}" method="POST"
                                              onsubmit="return confirm('Hapus complaint {{ $c->no_customer }}?')">
                                            @csrf @method('DELETE')
                                            <button class="w-full text-left px-3 py-2 text-sm text-rose-600 hover:bg-rose-50">🗑️ Hapus</button>
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

    @if ($complaints->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $complaints->links() }}
        </div>
    @endif
</div>

{{-- Modal Validasi CAPA & Persetujuan NCR Supervisor --}}
@can('approve-ncr')
<div id="approveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl my-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-violet-700 to-indigo-600 flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                🛡️ Validasi CAPA &amp; Persetujuan NCR Supervisor
            </h3>
            <button onclick="document.getElementById('approveModal').classList.add('hidden')" class="text-white/70 hover:text-white text-xl font-bold">&times;</button>
        </div>

        <form id="approveForm" method="POST" action="" class="p-6 space-y-4 max-h-[85vh] overflow-y-auto">
            @csrf
            @method('PATCH')

            {{-- Summary Identitas Komplain --}}
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 space-y-2.5 text-xs">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <div>
                        <span class="text-[10px] text-slate-400 font-extrabold uppercase">Customer</span>
                        <div class="font-bold text-slate-800 text-sm" id="approveCustomerName">-</div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-extrabold uppercase">No. Registrasi NCR</span>
                        <div class="font-mono font-bold text-sky-700 text-sm" id="approveNoCustomerBadge">-</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <div>
                            <span class="text-[10px] text-slate-400 font-extrabold uppercase">Jenis Ketidaksesuaian</span>
                            <div class="font-bold text-indigo-700 text-xs" id="approveKetidaksesuaian">-</div>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-extrabold uppercase">Detail Ketidaksesuaian</span>
                            <div class="text-xs text-slate-700 font-medium bg-white p-1.5 rounded border border-slate-200 min-h-[26px]" id="approveDetailKetidaksesuaian">-</div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div>
                            <span class="text-[10px] text-slate-400 font-extrabold uppercase">Penyebab Teridentifikasi</span>
                            <div class="font-bold text-amber-700 text-xs" id="approvePenyebab">-</div>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-extrabold uppercase">Detail Penyebab</span>
                            <div class="text-xs text-slate-700 font-medium bg-white p-1.5 rounded border border-slate-200 min-h-[26px]" id="approveDetailPenyebab">-</div>
                        </div>
                    </div>
                </div>

                {{-- Deskripsi & Kronologi Keluhan Customer --}}
                <div class="border-t border-slate-200/80 pt-2 mt-2">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wide flex items-center gap-1">
                            <span>📝</span> Deskripsi &amp; Rincian Keluhan Customer
                        </span>
                        <span class="text-[10px] text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded font-mono font-bold border border-indigo-100" id="approveSpecsQty">-</span>
                    </div>
                    <div class="text-xs text-slate-800 font-medium bg-amber-50/70 p-2.5 rounded-lg border border-amber-200/80 whitespace-pre-line leading-relaxed min-h-[38px]" id="approveDeskripsi">
                        -
                    </div>
                </div>

                {{-- Deskripsi Narasi Penyebab NCR --}}
                <div class="border-t border-slate-200/80 pt-2 mt-2">
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wide flex items-center gap-1 mb-1">
                        <span>🔍</span> Deskripsi Narasi Penyebab (Dicetak ke Surat NCR PDF)
                    </span>
                    <div class="text-xs text-slate-800 font-medium bg-sky-50/70 p-2.5 rounded-lg border border-sky-200/80 whitespace-pre-line leading-relaxed min-h-[38px]" id="approveDeskripsiPenyebab">
                        -
                    </div>
                </div>
            </div>

            {{-- FOKUS UTAMA: PENINJAUAN & VALIDASI CAPA --}}
            <div class="bg-violet-50/60 border-2 border-violet-200 rounded-xl p-4 space-y-3">
                <div class="flex items-center gap-2 border-b border-violet-200/80 pb-2">
                    <span class="text-base">📋</span>
                    <h4 class="font-bold text-violet-900 text-xs uppercase tracking-wide">Pemeriksaan &amp; Validasi Tindakan (CAPA)</h4>
                    <span class="ml-auto text-[10px] bg-violet-200 text-violet-800 font-bold px-2 py-0.5 rounded">Tugas Utama SPV</span>
                </div>

                {{-- Corrective Action --}}
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                        <span>🛠️ Corrective Action (Tindakan Penanganan Langsung)</span>
                        <span class="text-[10px] font-normal text-slate-500">Penanggulangan cacat saat ini</span>
                    </label>
                    <textarea name="corrective_action" id="approveCorrectiveInput" rows="2"
                              placeholder="Usulan tindakan perbaikan dari QA..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:ring-violet-500 focus:border-violet-500 bg-white font-medium text-slate-800"></textarea>
                </div>

                {{-- Preventive Action --}}
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                        <span>🛡️ Preventive Action (Tindakan Pencegahan Ulang)</span>
                        <span class="text-[10px] font-normal text-slate-500">Pencegahan masalah terulang</span>
                    </label>
                    <textarea name="preventive_action" id="approvePreventiveInput" rows="2"
                              placeholder="Usulan tindakan pencegahan dari QA..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:ring-violet-500 focus:border-violet-500 bg-white font-medium text-slate-800"></textarea>
                </div>
            </div>

            {{-- Keputusan Status Approval --}}
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">Keputusan Validasi Supervisor <span class="text-rose-500">*</span></label>
                <select name="approval_status" id="approveStatusSelect" class="w-full rounded-lg border-2 border-slate-300 px-3 py-2 text-sm focus:ring-violet-500 focus:border-violet-500 font-bold text-slate-800"
                        onchange="document.getElementById('keteranganBox').classList.toggle('hidden', this.value !== 'Approved')">
                    <option value="Approved">✅ Disetujui (Approved — CAPA Sah & Form NCR Terbit)</option>
                    <option value="Pending">⏳ Pending (Membutuhkan Peninjauan/Data Tambahan)</option>
                    <option value="Rejected">❌ Ditolak / Perlu Revisi CAPA oleh QA</option>
                </select>
            </div>

            {{-- Keterangan Penyelesaian — hanya wewenang SPV, muncul saat Approved --}}
            <div id="keteranganBox" class="bg-rose-50 border-2 border-rose-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-base">📋</span>
                    <h4 class="font-bold text-rose-900 text-xs uppercase tracking-wide">Keterangan Penyelesaian Akhir</h4>
                    <span class="ml-auto text-[10px] bg-rose-200 text-rose-800 font-bold px-2 py-0.5 rounded">Wewenang SPV</span>
                </div>
                <p class="text-[11px] text-rose-700 mb-3">Tentukan keputusan akhir penanganan komplain ini: apakah barang dikembalikan (Retur) atau cukup diberikan masukan/klarifikasi (Feedback) kepada customer.</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 bg-white cursor-pointer hover:border-emerald-400 hover:bg-emerald-50 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                        <input type="radio" name="keterangan" value="Feedback" class="accent-emerald-600 w-4 h-4" checked>
                        <div>
                            <div class="font-bold text-emerald-700 text-xs">🟢 Feedback</div>
                            <div class="text-[10px] text-slate-500 mt-0.5">Masukan / klarifikasi ke customer, tanpa pengembalian barang</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 bg-white cursor-pointer hover:border-rose-400 hover:bg-rose-50 transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50">
                        <input type="radio" name="keterangan" value="Retur" class="accent-rose-600 w-4 h-4">
                        <div>
                            <div class="font-bold text-rose-700 text-xs">🔴 Retur</div>
                            <div class="text-[10px] text-slate-500 mt-0.5">Barang cacat dikembalikan ke pabrik untuk penggantian</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Catatan SPV untuk QA --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Arahan Tambahan Supervisor (Tampil ke QA)</label>
                <textarea name="catatan_supervisor" id="approveCatatan" rows="2" placeholder="Masukkan arahan atau catatan revisi jika CAPA perlu diperbaiki QA..."
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:ring-violet-500 focus:border-violet-500"></textarea>
            </div>

            {{-- Section Visit Customer --}}
            <div class="border-t border-slate-200 pt-3">
                <label class="inline-flex items-center gap-2 cursor-pointer mb-2">
                    <input type="checkbox" name="perlu_visit" id="approvePerluVisit" value="1" onchange="toggleVisitFields(this.checked)" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500 w-4 h-4">
                    <span class="text-xs font-bold text-slate-800">🚐 Jadwalkan Kunjungan Lapangan (Visit Customer)</span>
                </label>

                <div id="visitFieldsBox" class="hidden space-y-3 pl-6 border-l-2 border-violet-300 mt-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Tanggal Rencana Kunjungan</label>
                            <input type="date" name="tanggal_visit" id="approveTanggalVisit" min="{{ date('Y-m-d') }}" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Jam Visit</label>
                            <input type="time" name="jam_visit" id="approveJamVisit" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Catatan Kunjungan Lapangan</label>
                        <input type="text" name="catatan_visit" id="approveCatatanVisit" placeholder="Misal: Cek fisik sampel Moisture Content di lokasi customer"
                               class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm py-2.5 rounded-lg shadow-sm transition">
                    Simpan Validasi CAPA &amp; NCR
                </button>
                <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm py-2.5 rounded-lg transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- Import Modal --}}
@can('manage-complaints')
<div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-amber-500 text-white flex items-center justify-between">
            <h3 class="text-base font-bold flex items-center gap-2">📥 Import Data Complaint Excel</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-white/70 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('complaints.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih File Excel (.xlsx / .xls)</label>
                <input type="file" name="file" required accept=".xlsx,.xls" class="w-full text-sm border border-slate-300 rounded-lg p-2">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 rounded-lg text-sm">Upload & Import</button>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-700 py-2 rounded-lg text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script>
document.querySelectorAll('.row-menu').forEach(menu => {
    const btn = menu.querySelector('.menu-btn');
    const panel = menu.querySelector('.menu-panel');

    btn?.addEventListener('click', (e) => {
        e.stopPropagation();
        document.querySelectorAll('.menu-panel').forEach(p => p !== panel && p.classList.add('hidden'));
        panel?.classList.toggle('hidden');
    });
});

document.addEventListener('click', () => {
    document.querySelectorAll('.menu-panel').forEach(p => p.classList.add('hidden'));
});

function toggleVisitFields(checked) {
    const box = document.getElementById('visitFieldsBox');
    if (box) {
        if (checked) box.classList.remove('hidden');
        else box.classList.add('hidden');
    }
}

function openApproveModal(id, noCust, currentStatus, currentCatatan, perluVisit, tanggalVisit, jamVisit, catatanVisit, customerName, ketidaksesuaian, detailKetidaksesuaian, penyebab, detailPenyebab, corrective, preventive, deskripsi, ukuran, qty, deskripsiPenyebab) {
    const form = document.getElementById('approveForm');
    form.action = `/complaints/${id}/approve`;
    
    document.getElementById('approveCustomerName').innerText = customerName || '-';
    document.getElementById('approveNoCustomerBadge').innerText = noCust || '-';
    document.getElementById('approveKetidaksesuaian').innerText = ketidaksesuaian || '-';
    document.getElementById('approveDetailKetidaksesuaian').innerText = detailKetidaksesuaian || '-';
    document.getElementById('approvePenyebab').innerText = penyebab || '-';
    document.getElementById('approveDetailPenyebab').innerText = detailPenyebab || '-';

    document.getElementById('approveDeskripsi').innerText = (deskripsi && deskripsi.trim() !== '') ? deskripsi : 'Tidak ada deskripsi rinci dari customer/QA.';
    const deskPenEl = document.getElementById('approveDeskripsiPenyebab');
    if (deskPenEl) {
        deskPenEl.innerText = (deskripsiPenyebab && deskripsiPenyebab.trim() !== '') ? deskripsiPenyebab : (penyebab || 'Tidak ada deskripsi narasi penyebab.');
    }
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

    document.getElementById('approveModal').classList.remove('hidden');
}
</script>
@endpush
