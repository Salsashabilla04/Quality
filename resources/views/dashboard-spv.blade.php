@extends('layouts.app')
@section('title', 'Dashboard Supervisor QC')
@section('subtitle', 'Monitoring Manajerial · NCR Approval · Kunjungan · Analisis Kualitas')

@section('actions')
    <div class="flex items-center gap-3">
         {{-- Laporan PDF --}}
        <a href="{{ route('export.laporan.pdf') }}"
           class="inline-flex items-center justify-center gap-2
                  h-10 px-5
                  rounded-xl
                  bg-rose-600 hover:bg-rose-700
                  text-white text-sm font-semibold
                  shadow-sm
                  transition-all duration-200
                  whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 10v6m0 0 3-3m-3 3-3-3M5 20h14a2 2 0 002-2V8l-5-5H7a2 2 0 00-2 2v13a2 2 0 002 2z"/>
            </svg>

            <span>Laporan PDF</span>
        </a>
    </div>
@endsection

@section('content')
@php
    $today = now()->startOfDay();
@endphp

{{-- ═══════════════════════════════════════════════════
     BARIS 1 — ACTION BANNERS
════════════════════════════════════════════════════ --}}

{{-- NCR Menunggu Approval --}}
@if($ncrPending > 0)
<div class="mb-4 bg-purple-50 border-2 border-purple-300 rounded-2xl p-5 shadow-sm flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-extrabold text-xl shrink-0">⏳</div>
        <div>
            <div class="text-sm font-bold text-purple-900">🛡️ APPROVAL PENDING: {{ $ncrPending }} Dokumen NCR Menunggu Persetujuan Anda!</div>
            <div class="text-xs text-purple-700 mt-0.5">Tinjau kelayakan data investigasi 6M & CAPA sebelum memberikan approval.</div>
        </div>
    </div>
    <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}"
       class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm whitespace-nowrap">
        Tinjau Kasus Pending →
    </a>
</div>
@endif

{{-- NCR Dikembalikan / Revisi --}}
@if($ncrRejected > 0)
<div class="mb-4 bg-rose-50 border-2 border-rose-300 rounded-2xl p-5 shadow-sm flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-full bg-rose-600 text-white flex items-center justify-center font-extrabold text-xl shrink-0">!</div>
        <div>
            <div class="text-sm font-bold text-rose-900">⚠️ {{ $ncrRejected }} Dokumen NCR telah Anda kembalikan untuk Perbaikan Staff QA</div>
            <div class="text-xs text-rose-700 mt-0.5">Pantau proses revisi — dokumen akan kembali ke antrian approval setelah diperbaiki.</div>
        </div>
    </div>
    <a href="{{ route('complaints.index', ['approval' => 'Rejected']) }}"
       class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm whitespace-nowrap">
        Pantau Revisi →
    </a>
</div>
@endif

{{-- Kunjungan Terdekat Alert --}}
@if($visitActiveTotal > 0)
@php $soonestVisit = $upcomingVisits->first(); @endphp
<div class="mb-6 bg-indigo-50 border-2 border-indigo-300 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <span class="text-2xl">🚐</span>
        <div>
            <div class="text-sm font-bold text-indigo-900">JADWAL VISIT: {{ $visitActiveTotal }} kunjungan lapangan aktif</div>
            @if($soonestVisit && $soonestVisit->tanggal_visit)
            @php
                $diff = now()->startOfDay()->diffInDays($soonestVisit->tanggal_visit, false);
                $hMinus = $diff == 0 ? '🔴 Hari Ini!' : ($diff < 0 ? '⚠️ Sudah Lewat' : "H-{$diff}");
            @endphp
            <div class="text-xs text-indigo-700 mt-0.5">
                Terdekat: <span class="font-semibold">{{ $soonestVisit->nama_customer }}</span>
                — {{ $soonestVisit->tanggal_visit->format('d M Y') }}
                <span class="ml-1 font-bold text-indigo-900">{{ $hMinus }}</span>
            </div>
            @endif
        </div>
    </div>
    <a href="{{ route('visit.index') }}" class="text-xs font-bold text-indigo-800 underline whitespace-nowrap">Buka Kalender Visit →</a>
</div>
@endif

{{-- ═══════════════════════════════════════════════════
     BARIS 2 — KPI CARDS EXECUTIVE
════════════════════════════════════════════════════ --}}
@php
$kpiCards = [
    ['Total Complaint',    $kpi['total_complaint'],                         'kasus (all time)',      'bg-sky-500',     route('complaints.index')],
    ['NCR Divalidasi ✅',  $ncrApproved,                                    'dokumen approved',      'bg-emerald-500', route('complaints.index', ['approval' => 'Approved'])],
    ['NCR Pending ⏳',     $ncrPending,                                     'menunggu review',       'bg-purple-500',  route('complaints.index', ['approval' => 'Pending'])],
    ['NCR Revisi 🔄',      $ncrRejected,                                    'dikembalikan ke QA',    'bg-rose-500',    route('complaints.index', ['approval' => 'Rejected'])],
    ['Status Close',       $kpi['close'].' / '.$kpi['total_complaint'],     $kpi['persen_close'].'% selesai', 'bg-teal-500', route('complaints.index', ['status' => 'Close'])],
    ['Jumlah Customer',    $kpi['jumlah_customer'],                         'pihak/klien',           'bg-indigo-500',  route('customers.index')],
    ['Total Qty NG',       number_format($kpi['total_qty']),                'pcs terdeteksi',        'bg-orange-500',  null],
    ['Visit Aktif',        $visitActiveTotal,                               'agenda kunjungan',      'bg-slate-500',   route('visit.index')],
];
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach($kpiCards as [$label, $value, $sub, $color, $url])
    @if($url)
        <a href="{{ $url }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md hover:border-slate-300 transition-all group block">
            <div class="flex items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $color }} shrink-0"></span>
                    <span class="text-xs text-slate-600 font-semibold group-hover:text-purple-600 transition-colors leading-tight">{{ $label }}</span>
                </div>
                <span class="text-xs text-slate-300 group-hover:text-purple-500 transition-colors">→</span>
            </div>
            <div class="text-2xl font-bold text-slate-900 truncate">{{ $value }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{!! $sub !!}</div>
        </a>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2.5 h-2.5 rounded-full {{ $color }} shrink-0"></span>
                <span class="text-xs text-slate-500 font-medium leading-tight">{{ $label }}</span>
            </div>
            <div class="text-2xl font-bold text-slate-900 truncate">{{ $value }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{!! $sub !!}</div>
        </div>
    @endif
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════
     BARIS 3 — UPCOMING CUSTOMER VISITS
════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-slate-50">
        <div class="flex items-center gap-2">
            <span class="text-lg">🚐</span>
            <h2 class="font-semibold text-slate-800">Jadwal Kunjungan Lapangan</h2>
            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-medium">{{ $visitActiveTotal }} aktif</span>
        </div>
        <a href="{{ route('visit.index') }}" class="text-xs text-indigo-600 hover:underline font-medium">Lihat semua →</a>
    </div>

    @if($upcomingVisits->isEmpty())
        <div class="px-5 py-8 text-center text-slate-400 text-sm">Tidak ada jadwal kunjungan aktif.</div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs uppercase text-slate-500 bg-slate-50">
                    <th class="px-5 py-3 text-left">No Complaint</th>
                    <th class="px-5 py-3 text-left">Customer / PT</th>
                    <th class="px-5 py-3 text-center">Tanggal Visit</th>
                    <th class="px-5 py-3 text-center">H-Minus</th>
                    <th class="px-5 py-3 text-left">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($upcomingVisits as $v)
                @php
                    $diff = $v->tanggal_visit ? now()->startOfDay()->diffInDays($v->tanggal_visit, false) : null;
                    if ($diff === null) { $badge = '—'; $badgeClass = 'bg-slate-100 text-slate-500'; }
                    elseif ($diff == 0) { $badge = 'HARI INI'; $badgeClass = 'bg-rose-100 text-rose-700 font-bold'; }
                    elseif ($diff < 0) { $badge = 'Lewat '.abs($diff).'h'; $badgeClass = 'bg-orange-100 text-orange-700'; }
                    elseif ($diff <= 3) { $badge = "H-{$diff}"; $badgeClass = 'bg-amber-100 text-amber-700 font-bold'; }
                    else { $badge = "H-{$diff}"; $badgeClass = 'bg-indigo-100 text-indigo-700'; }
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-mono text-xs text-slate-600">{{ $v->no_customer }}</td>
                    <td class="px-5 py-3 font-semibold text-slate-800">{{ $v->nama_customer }}</td>
                    <td class="px-5 py-3 text-center text-slate-600">{{ $v->tanggal_visit ? $v->tanggal_visit->format('d M Y') : '—' }}{{ $v->jam_visit ? ' · ' . substr($v->jam_visit, 0, 5) : '' }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs {{ $badgeClass }}">{{ $badge }}</span>
                    </td>
                    <td class="px-5 py-3 text-xs text-slate-500 max-w-xs truncate">{{ $v->catatan_visit ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════
     BARIS 4 — ANALISIS QC: PARETO + FISHBONE + APRIORI
════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
    {{-- Header section --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-sky-50 to-slate-50">
        <div class="flex items-center gap-2">
            <span class="text-lg">🔬</span>
            <h2 class="font-semibold text-slate-800">Analisis Kualitas — Seven Tools &amp; Apriori</h2>
            <span class="text-xs bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full font-medium">Insight Otomatis</span>
        </div>
        <a href="{{ route('seven-tools') }}" class="text-xs text-sky-600 hover:underline font-medium">Lihat Seven Tools lengkap →</a>
    </div>

    <div class="p-5">
        {{-- Sub-label --}}
        <p class="text-xs text-slate-500 mb-4">Alur baca: <b class="text-slate-700">Pareto</b> (defect apa yang paling banyak?) → <b class="text-slate-700">Apriori</b> (pola apa yang sering muncul bersamaan?). Untuk analisis Fishbone 6M lengkap, lihat halaman <a href="{{ route('seven-tools') }}#fishbone-section" class="text-sky-600 hover:underline font-semibold">Seven Tools</a>.</p>

        {{-- Pareto (Atas — Proporsional Tentu & Tidak Gepeng) --}}
        <div class="border border-slate-100 rounded-xl overflow-hidden mb-6 bg-white shadow-sm">
            {{-- Tab buttons --}}
            <div class="flex border-b border-slate-200 bg-slate-50 overflow-x-auto">
                @php
                $paretoTabs = [
                    ['spvP-jenis',  '📊 Ketidaksesuaian'],
                    ['spvP-detail', '🔍 Detail KS'],
                    ['spvP-cust',   '🏢 Customer'],
                    ['spvP-cause',  '⚠️ Penyebab'],
                ];
                @endphp
                @foreach($paretoTabs as $idx => [$tabId, $tabLabel])
                <button onclick="switchParetoTab('{{ $tabId }}')"
                        id="btn-{{ $tabId }}"
                        class="pareto-tab-btn px-4 py-3 text-xs font-bold whitespace-nowrap border-b-2 transition-colors
                               {{ $idx === 0 ? 'border-sky-500 text-sky-700 bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100' }}">
                    {{ $tabLabel }}
                </button>
                @endforeach
            </div>

            {{-- Tab panels --}}
            <div class="p-5">
                @php
                $paretoPanels = [
                    ['spvP-jenis',  $paretoJenis,  '#0ea5e9', 'Jenis Ketidaksesuaian — frekuensi dari yang terbanyak (aturan 80/20).',           true],
                    ['spvP-detail', $paretoDetail, '#f59e0b', 'Detail Ketidaksesuaian — uraian spesifik tiap cacat.',                              false],
                    ['spvP-cust',   $paretoCust,   '#6366f1', 'Customer — siapa yang paling banyak mengajukan complaint.',                         false],
                    ['spvP-cause',  $paretoCause,  '#10b981', 'Penyebab Masalah — faktor dominan penyebab ketidaksesuaian.',                       false],
                ];
                @endphp
                @foreach($paretoPanels as [$panelId, $paretoData, $barColor, $desc, $show])
                <div id="{{ $panelId }}" class="pareto-panel {{ $show ? '' : 'hidden' }}">
                    <p class="text-xs text-slate-500 mb-3">{{ $desc }}</p>
                    <div class="relative h-[360px]">
                        @if(empty($paretoData['labels']))
                            <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data.</div>
                        @else
                            <canvas id="canvas-{{ $panelId }}"></canvas>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Divider --}}
        <div class="border-t border-slate-100 pt-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-slate-700 text-sm">📊 Grafik Pola Keterkaitan Cacat (Lift Ratio — Apriori)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tingkat kekuatan kaitan sebab-akibat antara jenis ketidaksesuaian dan penyebab utama.</p>
                </div>
                <a href="{{ route('apriori') }}" class="text-xs text-sky-600 hover:underline font-medium whitespace-nowrap ml-4">Analisis lengkap →</a>
            </div>

            @if(empty($topRules))
                <p class="text-sm text-slate-400 text-center py-4">Belum ada rule. Tambah data complaint untuk melihat hasil.</p>
            @else
                <div class="relative h-64 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                    <canvas id="spvAprioriChart"></canvas>
                </div>
                <div class="flex flex-wrap items-center justify-between text-[11px] text-slate-400 mt-3 gap-2">
                    <span>Berdasarkan <b>{{ $totalTrx }}</b> transaksi komplain · Terurut dari kaitan terkuat</span>
                    <div class="flex items-center gap-3 font-medium">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-rose-500 inline-block"></span> Sangat Kuat (&ge;5x)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-amber-500 inline-block"></span> Kuat (&ge;3x)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-sky-500 inline-block"></span> Sedang (&ge;1.5x)</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     BARIS 6 — MINI LAPORAN VISUAL
════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-violet-50 to-slate-50">
        <div class="flex items-center gap-2">
            <span class="text-lg">📋</span>
            <h2 class="font-semibold text-slate-800">Laporan Visual {{ $tahunLaporan }}</h2>
            <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-medium">Ringkasan Tahun Berjalan</span>
        </div>
        <a href="{{ route('laporan') }}" class="text-xs text-violet-600 hover:underline font-medium">Laporan lengkap →</a>
    </div>

    <div class="p-5">
        {{-- KPI Mini --}}
        @php
        $miniKpis = [
            ['Total Complaint', $totalComplaint, 'bg-indigo-500'],
            ['Open',            $totalOpen,      'bg-amber-400'],
            ['Diproses',        $totalDiproses,  'bg-sky-400'],
            ['Close',           $totalClose,     'bg-emerald-400'],
            ['Qty NG',          number_format($totalQtyTahun), 'bg-rose-500'],
        ];
        @endphp
        <div class="grid grid-cols-5 gap-3 mb-5">
            @foreach($miniKpis as [$lbl, $val, $cls])
            <div class="text-center">
                <div class="text-xl font-bold text-slate-800">{{ $val }}</div>
                <div class="flex items-center justify-center gap-1 mt-1">
                    <span class="w-2 h-2 rounded-full {{ $cls }}"></span>
                    <span class="text-[11px] text-slate-500">{{ $lbl }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Trend --}}
            <div class="lg:col-span-2">
                <h3 class="text-xs font-semibold text-slate-600 mb-3 uppercase tracking-wide">📈 Trend Bulanan {{ $tahunLaporan }}</h3>
                <canvas id="spvTrendChart" height="80"></canvas>
            </div>

            {{-- Status Doughnut --}}
            <div>
                <h3 class="text-xs font-semibold text-slate-600 mb-3 uppercase tracking-wide">🥧 Distribusi Status</h3>
                <canvas id="spvStatusChart" height="160"></canvas>
                <div class="mt-3 space-y-1">
                    @foreach(['Open' => [$totalOpen, 'bg-amber-400'], 'Diproses' => [$totalDiproses, 'bg-sky-400'], 'Close' => [$totalClose, 'bg-emerald-400']] as $s => [$n, $cls])
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-sm {{ $cls }}"></span>{{ $s }}
                        </span>
                        <span class="font-semibold text-slate-700">{{ $n }}
                            <span class="text-slate-400 font-normal">({{ $totalComplaint ? round($n / $totalComplaint * 100) : 0 }}%)</span>
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Top Customer Mini-Table --}}
        @if($topCustomer->isNotEmpty())
        <div class="mt-5 border-t border-slate-100 pt-4">
            <h3 class="text-xs font-semibold text-slate-600 mb-3 uppercase tracking-wide">🏆 Top Customer {{ $tahunLaporan }}</h3>
            <div class="space-y-2">
                @foreach($topCustomer as $i => $c)
                @php $pct = $totalComplaint ? round($c->total / $totalComplaint * 100) : 0; @endphp
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 w-4 text-right shrink-0">{{ $i + 1 }}</span>
                    <span class="text-xs font-medium text-slate-700 w-40 truncate shrink-0">{{ $c->nama_customer }}</span>
                    <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-700 w-8 text-right shrink-0">{{ $c->total }}</span>
                    <span class="text-[10px] text-slate-400 w-8 text-right shrink-0">{{ $pct }}%</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal Approval Supervisor --}}
@can('approve-ncr')
<div id="approveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-purple-600 to-indigo-600 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xl">🛡️</span>
                <div>
                    <h3 class="text-base font-bold leading-tight">Validasi CAPA &amp; Approval NCR</h3>
                    <p class="text-xs text-purple-100">Persetujuan Supervisor QC untuk investigasi &amp; penerbitan Surat NCR</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="text-white/70 hover:text-white text-xl font-bold">&times;</button>
        </div>

        <form id="approveForm" method="POST" action="" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            @csrf
            @method('PATCH')

            {{-- Info Singkat Komplain --}}
            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200 text-xs space-y-2">
                <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                    <div>
                        <span class="text-[10px] text-slate-400 font-extrabold uppercase">Nama Customer / Klien</span>
                        <div class="font-bold text-slate-800 text-sm" id="approveCustomerName">-</div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-extrabold uppercase">No. Registrasi NCR</span>
                        <div class="font-mono font-bold text-purple-700 text-sm" id="approveNoCustomerBadge">-</div>
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
                        <span class="text-[10px] text-purple-700 bg-purple-50 px-2 py-0.5 rounded font-mono font-bold border border-purple-100" id="approveSpecsQty">-</span>
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
            <div class="bg-purple-50/60 border-2 border-purple-200 rounded-xl p-4 space-y-3">
                <div class="flex items-center gap-2 border-b border-purple-200/80 pb-2">
                    <span class="text-base">📋</span>
                    <h4 class="font-bold text-purple-900 text-xs uppercase tracking-wide">Pemeriksaan &amp; Validasi Tindakan (CAPA)</h4>
                    <span class="ml-auto text-[10px] bg-purple-200 text-purple-800 font-bold px-2 py-0.5 rounded">Tugas Utama SPV</span>
                </div>

                {{-- Corrective Action --}}
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                        <span>🛠️ Corrective Action (Tindakan Penanganan Langsung)</span>
                        <span class="text-[10px] font-normal text-slate-500">Penanggulangan cacat saat ini</span>
                    </label>
                    <textarea name="corrective_action" id="approveCorrectiveInput" rows="2"
                              placeholder="Usulan tindakan perbaikan dari QA..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:ring-purple-500 focus:border-purple-500 bg-white font-medium text-slate-800"></textarea>
                </div>

                {{-- Preventive Action --}}
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                        <span>🛡️ Preventive Action (Tindakan Pencegahan Ulang)</span>
                        <span class="text-[10px] font-normal text-slate-500">Pencegahan masalah terulang</span>
                    </label>
                    <textarea name="preventive_action" id="approvePreventiveInput" rows="2"
                              placeholder="Usulan tindakan pencegahan dari QA..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:ring-purple-500 focus:border-purple-500 bg-white font-medium text-slate-800"></textarea>
                </div>
            </div>

            {{-- Keputusan Status Approval --}}
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">Keputusan Validasi Supervisor <span class="text-rose-500">*</span></label>
                <select name="approval_status" id="approveStatusSelect" class="w-full rounded-lg border-2 border-slate-300 px-3 py-2 text-sm focus:ring-purple-500 focus:border-purple-500 font-bold text-slate-800"
                        onchange="document.getElementById('keteranganBoxSpv').classList.toggle('hidden', this.value !== 'Approved')">
                    <option value="Approved">✅ Disetujui (Approved — CAPA Sah & Form NCR Terbit)</option>
                    <option value="Pending">⏳ Pending (Membutuhkan Peninjauan/Data Tambahan)</option>
                    <option value="Rejected">❌ Ditolak / Perlu Revisi CAPA oleh QA</option>
                </select>
            </div>

            {{-- Keterangan Penyelesaian — hanya wewenang SPV, muncul saat Approved --}}
            <div id="keteranganBoxSpv" class="bg-rose-50 border-2 border-rose-200 rounded-xl p-4">
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
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:ring-purple-500 focus:border-purple-500"></textarea>
            </div>

            {{-- Section Visit Customer --}}
            <div class="border-t border-slate-200 pt-3">
                <label class="inline-flex items-center gap-2 cursor-pointer mb-2">
                    <input type="checkbox" name="perlu_visit" id="approvePerluVisit" value="1" onchange="toggleVisitFieldsSpv(this.checked)" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 w-4 h-4">
                    <span class="text-xs font-bold text-slate-800">🚐 Jadwalkan Kunjungan Lapangan (Visit Customer)</span>
                </label>

                <div id="visitFieldsBoxSpv" class="hidden space-y-3 pl-6 border-l-2 border-purple-300 mt-2">
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
                <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm py-2.5 rounded-lg shadow-sm transition">
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

@push('scripts')
<script>
// ── Pareto Tab System — 4 charts ─────────────────────────────────────────────
const spvParetoData = {
    'spvP-jenis':  @json($paretoJenis),
    'spvP-detail': @json($paretoDetail),
    'spvP-cust':   @json($paretoCust),
    'spvP-cause':  @json($paretoCause),
};
const spvParetoColors = {
    'spvP-jenis':  '#0ea5e9',
    'spvP-detail': '#f59e0b',
    'spvP-cust':   '#6366f1',
    'spvP-cause':  '#10b981',
};
const spvParetoCharts = {};

function buildParetoChart(panelId) {
    if (spvParetoCharts[panelId]) return;
    const canvas = document.getElementById('canvas-' + panelId);
    if (!canvas) return;
    const d = spvParetoData[panelId];
    if (!d || !d.labels || d.labels.length === 0) return;
    spvParetoCharts[panelId] = new Chart(canvas, {
        data: {
            labels: d.labels,
            datasets: [
                { type:'bar',  label:'Jumlah',     data: d.values,     backgroundColor: spvParetoColors[panelId], yAxisID:'y',  order:2 },
                { type:'line', label:'Kumulatif %', data: d.cumulative, borderColor:'#ef4444', backgroundColor:'#ef4444',
                  yAxisID:'y1', tension:.3, order:1, pointRadius:3 }
            ]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
            scales:{
                x:{ ticks:{ font:{size:9}, maxRotation:60, minRotation:45 } },
                y:{ beginAtZero:true, title:{ display:true, text:'Frekuensi' } },
                y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false},
                     title:{ display:true, text:'%' } }
            }
        }
    });
}

function switchParetoTab(activeId) {
    document.querySelectorAll('.pareto-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.pareto-tab-btn').forEach(b => {
        b.classList.remove('border-sky-500', 'text-sky-700', 'bg-white');
        b.classList.add('border-transparent', 'text-slate-500');
    });
    document.getElementById(activeId)?.classList.remove('hidden');
    const btn = document.getElementById('btn-' + activeId);
    if (btn) {
        btn.classList.add('border-sky-500', 'text-sky-700', 'bg-white');
        btn.classList.remove('border-transparent', 'text-slate-500');
    }
    buildParetoChart(activeId);
}

document.addEventListener('DOMContentLoaded', () => buildParetoChart('spvP-jenis'));

// ── Apriori Chart SPV ─────────────────────────────────────────────────────────
const spvTopRules = @json($topRules);
const aprioriCanvas = document.getElementById('spvAprioriChart');
if (aprioriCanvas && spvTopRules.length > 0) {
    const labels = spvTopRules.map(r => r.antecedents + ' ➔ ' + r.consequents);
    const dataLift = spvTopRules.map(r => r.lift);
    const bgColors = spvTopRules.map(r => {
        if (r.lift >= 5) return '#ef4444';
        if (r.lift >= 3) return '#f59e0b';
        if (r.lift >= 1.5) return '#0ea5e9';
        return '#94a3b8';
    });

    new Chart(aprioriCanvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Kekuatan Kaitan (Lift Ratio)',
                data: dataLift,
                backgroundColor: bgColors,
                borderRadius: 6,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const r = spvTopRules[ctx.dataIndex];
                            const pct = Math.round(r.confidence * 100);
                            return [
                                ` Lift: ${r.lift.toFixed(1)}x (${r.kekuatan})`,
                                ` Akurasi / Kepastian: ${pct}%`,
                                ` "Jika timbul ${r.antecedents}, ${pct}% kasus disebabkan oleh ${r.consequents}"`
                            ];
                        }
                    }
                },
                datalabels: {
                    display: true,
                    anchor: 'end',
                    align: 'end',
                    color: '#475569',
                    font: { size: 10, weight: 'bold' },
                    formatter: (v) => v.toFixed(1) + 'x'
                }
            },
            layout: { padding: { right: 45 } },
            scales: {
                x: {
                    beginAtZero: true,
                    title: { display: true, text: 'Lift Ratio', font: { size: 10 } },
                    grid: { color: '#f1f5f9' }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: '600' } }
                }
            }
        }
    });
}

// ── Trend Chart ───────────────────────────────────────────────────────────────
new Chart(document.getElementById('spvTrendChart'), {
    type: 'line',
    data: {
        labels: @json($trendLabels),
        datasets: [{
            label: 'Complaint',
            data: @json($trendData),
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
            borderWidth: 2.5, pointBackgroundColor: '#6366f1', pointRadius: 4, tension: 0.4, fill: true,
        }]
    },
    options: {
        responsive:true,
        plugins:{ legend:{ display:false } },
        scales:{
            y:{ beginAtZero:true, ticks:{ stepSize:1 }, grid:{ color:'#f1f5f9' } },
            x:{ grid:{ display:false } }
        }
    }
});

// ── Status Doughnut ───────────────────────────────────────────────────────────
new Chart(document.getElementById('spvStatusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Open', 'Diproses', 'Close'],
        datasets: [{
            data: [{{ $totalOpen }}, {{ $totalDiproses }}, {{ $totalClose }}],
            backgroundColor: ['#fbbf24','#38bdf8','#34d399'],
            borderWidth: 0, hoverOffset: 4
        }]
    },
    options: { responsive:true, cutout:'65%', plugins:{ legend:{ display:false } } }
});

function toggleVisitFieldsSpv(checked) {
    const box = document.getElementById('visitFieldsBoxSpv');
    if (box) {
        if (checked) box.classList.remove('hidden');
        else box.classList.add('hidden');
    }
}

function openApproveModal(id, noCust, currentStatus, currentCatatan, perluVisit, tanggalVisit, jamVisit, catatanVisit, customerName, ketidaksesuaian, detailKetidaksesuaian, penyebab, detailPenyebab, corrective, preventive, deskripsi, ukuran, qty, deskripsiPenyebab) {
    const form = document.getElementById('approveForm');
    if (!form) return;
    form.action = `/complaints/${id}/approve`;

    document.getElementById('approveCustomerName').innerText = customerName || '-';
    document.getElementById('approveNoCustomerBadge').innerText = noCust || '-';
    document.getElementById('approveKetidaksesuaian').innerText = ketidaksesuaian || '-';
    document.getElementById('approveDetailKetidaksesuaian').innerText = detailKetidaksesuaian || '-';
    document.getElementById('approvePenyebab').innerText = penyebab || '-';
    document.getElementById('approveDetailPenyebab').innerText = detailPenyebab || '-';

    const deskEl = document.getElementById('approveDeskripsi');
    if (deskEl) deskEl.innerText = (deskripsi && deskripsi.trim() !== '') ? deskripsi : 'Tidak ada deskripsi rinci dari customer/QA.';
    const deskPenEl = document.getElementById('approveDeskripsiPenyebab');
    if (deskPenEl) {
        deskPenEl.innerText = (deskripsiPenyebab && deskripsiPenyebab.trim() !== '') ? deskripsiPenyebab : (penyebab || 'Tidak ada deskripsi narasi penyebab.');
    }
    let specs = '';
    if (ukuran) specs += 'Ukuran: ' + ukuran;
    if (qty) specs += (specs ? ' | ' : '') + 'Qty: ' + qty + ' pcs';
    const specsEl = document.getElementById('approveSpecsQty');
    if (specsEl) specsEl.innerText = specs || 'Rincian: -';

    const statusSelect = document.getElementById('approveStatusSelect');
    statusSelect.value = (currentStatus && currentStatus !== 'null') ? currentStatus : 'Approved';
    const ketBox = document.getElementById('keteranganBoxSpv');
    if (ketBox) {
        ketBox.classList.toggle('hidden', statusSelect.value !== 'Approved');
    }
    document.getElementById('approveCatatan').value = currentCatatan || '';
    document.getElementById('approveCorrectiveInput').value = corrective || '';
    document.getElementById('approvePreventiveInput').value = preventive || '';

    const visitCheck = document.getElementById('approvePerluVisit');
    if (visitCheck) {
        visitCheck.checked = !!perluVisit;
        toggleVisitFieldsSpv(!!perluVisit);
    }
    const tglInput = document.getElementById('approveTanggalVisit');
    if (tglInput) tglInput.value = tanggalVisit || '';
    const jamInput = document.getElementById('approveJamVisit');
    if (jamInput) jamInput.value = jamVisit || '';
    const catVisitInput = document.getElementById('approveCatatanVisit');
    if (catVisitInput) catVisitInput.value = catatanVisit || '';

    document.getElementById('approveModal').classList.remove('hidden');
}
</script>
@endpush

@endsection
