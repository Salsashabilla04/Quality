@extends('layouts.app')
@section('title', 'Dashboard Supervisor QC')
@section('subtitle', 'Monitoring Manajerial · NCR Approval · Kunjungan · Analisis Kualitas')

@section('actions')
    <div class="flex items-center gap-2">
        {{-- Laporan PDF --}}
        <a href="{{ route('export.laporan.pdf', $selectedYear !== 'all' ? ['tahun' => $selectedYear] : []) }}" target="_blank" class="btn btn-danger btn-sm whitespace-nowrap">
            <i class="fas fa-file-pdf"></i>
            <span>Laporan PDF {{ $selectedYear !== 'all' ? '('.$selectedYear.')' : '' }}</span>
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
<div class="mb-4 bg-white border-l-4 border-l-[#6f42c1] rounded shadow-sm p-4 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-purple-100 text-[#6f42c1] flex items-center justify-center shrink-0">
            <i class="fas fa-clock text-lg"></i>
        </div>
        <div>
            <div class="text-sm font-bold text-gray-800">
                Approval Pending: {{ $ncrPending }} Dokumen NCR Menunggu Persetujuan Anda!
            </div>
             <div class="text-xs text-gray-500 mt-0.5">Tinjau kelayakan data investigasi 6M &amp; CAPA sebelum memberikan approval.</div>
        </div>
    </div>
    <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}" class="btn btn-info btn-sm whitespace-nowrap">
        <span>Tinjau Kasus Pending</span>
        <i class="fas fa-arrow-right"></i>
    </a>
</div>
@endif

{{-- NCR Dikembalikan / Revisi --}}
@if($ncrRejected > 0)
<div class="mb-4 bg-white border-l-4 border-l-[#e74a3b] rounded shadow-sm p-4 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-rose-100 text-[#e74a3b] flex items-center justify-center shrink-0">
            <i class="fas fa-exclamation-triangle text-lg"></i>
        </div>
        <div>
            <div class="text-sm font-bold text-gray-800">
                {{ $ncrRejected }} Dokumen NCR telah Anda kembalikan untuk Perbaikan Staff QA
            </div>
            <div class="text-xs text-gray-500 mt-0.5">Pantau proses revisi — dokumen akan kembali ke antrian approval setelah diperbaiki.</div>
        </div>
    </div>
    <a href="{{ route('complaints.index', ['approval' => 'Rejected']) }}" class="btn btn-danger btn-sm whitespace-nowrap">
        <span>Pantau Revisi</span>
        <i class="fas fa-arrow-right"></i>
    </a>
</div>
@endif

{{-- Kunjungan Terdekat Alert --}}
@if($visitActiveTotal > 0)
@php $soonestVisit = $upcomingVisits->first(); @endphp
<div class="mb-4 bg-white border-l-4 border-l-[#36b9cc] rounded shadow-sm p-4 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-cyan-100 text-[#36b9cc] flex items-center justify-center shrink-0">
            <i class="fas fa-map-marker-alt text-lg"></i>
        </div>
        <div>
            <div class="text-sm font-bold text-gray-800">Jadwal Visit: {{ $visitActiveTotal }} kunjungan lapangan aktif</div>
            @if($soonestVisit && $soonestVisit->tanggal_visit)
            @php
                $diff = now()->startOfDay()->diffInDays($soonestVisit->tanggal_visit, false);
                $hMinus = $diff == 0 ? 'Hari Ini!' : ($diff < 0 ? 'Sudah Lewat' : "H-{$diff}");
            @endphp
            <div class="text-xs text-gray-500 mt-0.5">
                Terdekat: <span class="font-semibold text-gray-700">{{ $soonestVisit->nama_customer }}</span>
                — {{ $soonestVisit->tanggal_visit->format('d M Y') }}
                <span class="ml-1 font-bold text-[#36b9cc]">({{ $hMinus }})</span>
            </div>
            @endif
        </div>
    </div>
    <a href="{{ route('visit.index') }}" class="btn btn-info btn-sm whitespace-nowrap">
        <span>Buka Kalender Visit</span>
        <i class="fas fa-arrow-right"></i>
    </a>
</div>
@endif

{{-- ═══════════════════════════════════════════════════
     BARIS 2 — KPI CARDS EXECUTIVE (SB ADMIN 2 STYLE)
════════════════════════════════════════════════════ --}}
@php
$kpiCards = [
    ['Total Complaint',    $kpi['total_complaint'],                         'kasus (all time)',      'border-left-primary', 'text-[#4e73df]', 'fa-clipboard-list',   route('complaints.index')],
    ['NCR Divalidasi',     $ncrApproved,                                    'dokumen approved',      'border-left-success', 'text-[#1cc88a]', 'fa-check-circle',      route('complaints.index', ['approval' => 'Approved'])],
    ['NCR Pending',        $ncrPending,                                     'menunggu review',       'border-left-purple',  'text-[#6f42c1]', 'fa-clock',             route('complaints.index', ['approval' => 'Pending'])],
    ['NCR Revisi',         $ncrRejected,                                    'dikembalikan ke QA',    'border-left-danger',  'text-[#e74a3b]', 'fa-exclamation-triangle', route('complaints.index', ['approval' => 'Rejected'])],
    ['Status Close',       $kpi['close'].' / '.$kpi['total_complaint'],     $kpi['persen_close'].'% selesai', 'border-left-info', 'text-[#36b9cc]', 'fa-tasks',   route('complaints.index', ['status' => 'Close'])],
    ['Jumlah Customer',    $kpi['jumlah_customer'],                         'pihak/klien',           'border-left-primary', 'text-[#4e73df]', 'fa-users',           route('customers.index')],
    ['Total Qty NG',       number_format($kpi['total_qty']),                'pcs terdeteksi',        'border-left-orange',  'text-[#fd7e14]', 'fa-boxes',           null],
    ['Visit Aktif',        $visitActiveTotal,                               'agenda kunjungan',      'border-left-indigo',  'text-[#5a5c69]', 'fa-truck',           route('visit.index')],
];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($kpiCards as [$label, $value, $sub, $borderClass, $textClass, $icon, $url])
    @if($url)
        <a href="{{ $url }}" class="bg-white rounded shadow-sm hover:shadow transition-shadow border-l-4 {{ $borderClass }} p-4 flex items-center justify-between group block">
            <div class="min-w-0 flex-1 pr-2">
                <div class="text-[11px] font-extrabold uppercase tracking-wider {{ $textClass }} mb-1 truncate">{{ $label }}</div>
                <div class="text-xl md:text-2xl font-black text-gray-800 leading-tight truncate">{{ $value }}</div>
                <div class="text-xs text-gray-500 mt-1 truncate">{!! $sub !!}</div>
            </div>
            <div class="shrink-0 text-gray-300 group-hover:text-gray-400 transition-colors pl-2">
                <i class="fas {{ $icon }} text-2xl md:text-3xl"></i>
            </div>
        </a>
    @else
        <div class="bg-white rounded shadow-sm border-l-4 {{ $borderClass }} p-4 flex items-center justify-between">
            <div class="min-w-0 flex-1 pr-2">
                <div class="text-[11px] font-extrabold uppercase tracking-wider {{ $textClass }} mb-1 truncate">{{ $label }}</div>
                <div class="text-xl md:text-2xl font-black text-gray-800 leading-tight truncate">{{ $value }}</div>
                <div class="text-xs text-gray-500 mt-1 truncate">{!! $sub !!}</div>
            </div>
            <div class="shrink-0 text-gray-300 pl-2">
                <i class="fas {{ $icon }} text-2xl md:text-3xl"></i>
            </div>
        </div>
    @endif
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════
     BARIS 6 — MINI LAPORAN VISUAL
════════════════════════════════════════════════════ --}}
<div id="laporanVisualCard" class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 overflow-hidden scroll-mt-6">
    {{-- Header + Filter Tahun / Bulan --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-violet-50 to-slate-50 gap-3">
        <div>
            <h2 class="font-bold text-[#4e73df] text-base flex items-center gap-2">
                <i class="fa-solid fa-chart-column shrink-0"></i>
                Laporan Visual
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Periode: <b class="text-slate-700">{{ $periodeLabel }}</b> · semua grafik di bawah mengikuti periode ini
            </p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}#laporanVisualCard" id="laporanFilterForm" class="flex items-end gap-2 flex-wrap">
            @foreach(request()->except(['laporan_year', 'laporan_month', 'laporan_from', 'laporan_to']) as $k => $v)
                @if(!is_array($v))
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach

            <div>
                <label for="laporanFromSelect" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Dari Tahun</label>
                <div class="relative">
                    <select name="laporan_from" id="laporanFromSelect" onchange="onLaporanRangeChange('from')"
                            class="appearance-none bg-white border border-slate-300 rounded-lg pl-3 pr-8 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:border-[#4e73df] focus:ring-2 focus:ring-[#4e73df]/30 focus:border-[#4e73df] cursor-pointer">
                        @foreach(array_reverse($laporanYears) as $y)
                            <option value="{{ $y }}" @selected($fromYear === $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            <span class="text-xs text-slate-400 pb-2">s/d</span>

            <div>
                <label for="laporanToSelect" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Sampai Tahun</label>
                <div class="relative">
                    <select name="laporan_to" id="laporanToSelect" onchange="onLaporanRangeChange('to')"
                            class="appearance-none bg-white border border-slate-300 rounded-lg pl-3 pr-8 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:border-[#4e73df] focus:ring-2 focus:ring-[#4e73df]/30 focus:border-[#4e73df] cursor-pointer">
                        @foreach(array_reverse($laporanYears) as $y)
                            <option value="{{ $y }}" @selected($toYear === $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-[11px] font-semibold text-slate-500" title="Rentang tahun dibatasi maksimal {{ $maxRange }} tahun">
                <i class="fas fa-circle-info text-[#4e73df]"></i> Maks. {{ $maxRange }} tahun
            </span>
        </form>
    </div>

    <div class="p-5">
        {{-- 3 Ringkasan --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            {{-- Total komplain periode --}}
            <div class="rounded-lg border border-slate-200 p-4">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Total Komplain</div>
                <div class="text-2xl font-black leading-tight text-slate-800">
                    {{ number_format($totalComplaint) }} <span class="text-base font-bold">kasus</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">dalam periode terpilih ({{ $periodeLabel }})</div>
            </div>

            {{-- Bulan tertinggi --}}
            <div class="rounded-lg border border-slate-200 p-4">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Bulan Tertinggi</div>
                <div class="text-2xl font-black leading-tight text-slate-800 truncate">
                    @if($peakMonth)
                        {{ $peakMonth }} <span class="text-slate-400 font-bold">·</span> {{ $peakCount }} <span class="text-base font-bold">kasus</span>
                    @else
                        <span class="text-slate-300">—</span>
                    @endif
                </div>
                <div class="text-xs text-slate-500 mt-1">dalam periode terpilih</div>
            </div>

            {{-- Rata-rata lead time --}}
            <div class="rounded-lg border border-slate-200 p-4">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Rata-rata Lead Time</div>
                <div class="text-2xl font-black leading-tight text-slate-800">
                    @if($avgLeadTime !== null)
                        {{ $avgLeadTime }} <span class="text-base font-bold">hari</span>
                    @else
                        <span class="text-slate-300">—</span>
                    @endif
                </div>
                <div class="text-xs text-slate-500 mt-1">complain → close ({{ $totalClose }} kasus close)</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Tren Komplain --}}
            <div class="lg:col-span-2 rounded-lg border border-slate-200 p-4">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Tren Komplain</h3>
                        <p class="text-[11px] text-slate-500">
                            @if($isSingleYear)
                                Jumlah komplain per bulan, {{ $fromYear }} vs {{ $fromYear - 1 }}
                            @else
                                Total komplain per tahun, {{ $periodeLabel }} ({{ $rangeCount }} tahun)
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(!$isSingleYear)
                            <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-1 rounded font-medium border border-slate-200">
                                {{ $rangeCount }} tahun ditampilkan · maks {{ $maxRange }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-600 shrink-0">
                            <i class="fas fa-layer-group text-[#4e73df]"></i> Level: {{ $isSingleYear ? 'Per Bulan' : 'Per Tahun' }}
                        </span>
                    </div>
                </div>
                <div class="relative h-[280px]">
                    <canvas id="spvTrendChart"></canvas>
                </div>
                <p class="text-[11px] text-slate-500 mt-3 leading-relaxed">
                    @if($isSingleYear)
                        Rentang satu tahun &rarr; grafik otomatis tampil <b>per bulan</b>. Ubah ke rentang (lebih dari 1 tahun) untuk melihat perbandingan antar tahun.
                    @else
                        Rentang dibatasi <b>{{ $maxRange }} tahun</b>, sehingga grafik ini maksimal {{ $maxRange }} batang dan tetap rapi &amp; mudah dibaca. Set tahun awal = tahun akhir untuk melihat rincian per bulan.
                    @endif
                </p>
            </div>

            <div class="space-y-5">
                {{-- Distribusi Status --}}
                @php
                    $statusRows = [
                        'Open'     => [$totalOpen,     'bg-amber-400'],
                        'Diproses' => [$totalDiproses, 'bg-sky-400'],
                        'Close'    => [$totalClose,    'bg-emerald-500'],
                    ];
                    $closingRate = $totalComplaint ? round($totalClose / $totalComplaint * 100) : 0;
                @endphp
                <div class="rounded-lg border border-slate-200 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-slate-800">Distribusi Status</h3>
                        <span class="text-[11px] text-slate-500">Closing rate <b class="text-emerald-600">{{ $closingRate }}%</b></span>
                    </div>
                    <div class="flex w-full h-3 rounded-full overflow-hidden bg-slate-100 mb-3">
                        @foreach($statusRows as $s => [$n, $cls])
                            @if($totalComplaint && $n)
                                <div class="{{ $cls }} h-full" style="width: {{ $n / $totalComplaint * 100 }}%" title="{{ $s }}: {{ $n }}"></div>
                            @endif
                        @endforeach
                    </div>
                    <div class="space-y-1.5">
                        @foreach($statusRows as $s => [$n, $cls])
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2 text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-sm {{ $cls }}"></span>{{ $s }}
                            </span>
                            <span class="flex items-center gap-4">
                                <span class="font-bold text-slate-800 w-8 text-right">{{ $n }}</span>
                                <span class="text-slate-400 w-9 text-right">{{ $totalComplaint ? round($n / $totalComplaint * 100) : 0 }}%</span>
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Top 5 Customer --}}
                <div class="rounded-lg border border-slate-200 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-slate-800">Top 5 Customer</h3>
                        <span class="text-[11px] text-slate-400">% dari total komplain</span>
                    </div>
                    @if($topCustomer->isEmpty())
                        <p class="text-xs text-slate-400 text-center py-3">Tidak ada data pada periode ini.</p>
                    @else
                    <div class="space-y-2.5">
                        @foreach($topCustomer as $i => $c)
                        @php $pct = $totalComplaint ? round($c->total / $totalComplaint * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center gap-2 text-xs mb-1">
                                <span class="text-slate-400 w-3 shrink-0">{{ $i + 1 }}</span>
                                <span class="font-medium text-slate-700 flex-1 truncate" title="{{ $c->nama_customer }}">{{ $c->nama_customer }}</span>
                                <span class="font-bold text-slate-800 w-7 text-right shrink-0">{{ $c->total }}</span>
                                <span class="text-slate-400 w-9 text-right shrink-0">{{ $pct }}%</span>
                            </div>
                            <div class="ml-5 bg-slate-100 rounded-full h-1.5">
                                <div class="bg-[#4e73df] h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <a href="{{ route('customers.index') }}" class="inline-block mt-3 text-xs text-sky-600 hover:underline font-medium">Lihat semua customer →</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     BARIS 4 — ANALISIS QC: PARETO + FISHBONE + APRIORI
════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
    {{-- Header section --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-sky-50 to-slate-50">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-chart-simple text-[#4e73df] text-xl shrink-0"></i>
            <h2 class="font-bold text-[#4e73df] text-base mb-0.5">Analisis Kualitas</h2>
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
                    ['spvP-jenis',  'Ketidaksesuaian'],
                    ['spvP-detail', 'Detail KS'],
                    ['spvP-cust',   'Customer'],
                    ['spvP-cause',  'Penyebab'],
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
                    <h3 class="font-semibold text-slate-700 text-sm">Grafik Pola Keterkaitan Cacat (Lift Ratio — Apriori)</h3>
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



{{-- Modal Approval Supervisor --}}
@can('approve-ncr')
<div id="approveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-purple-600 to-indigo-600 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
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
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wide flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                            <span>Deskripsi &amp; Rincian Keluhan Customer</span>
                        </span>
                        <span class="text-[10px] text-purple-700 bg-purple-50 px-2 py-0.5 rounded font-mono font-bold border border-purple-100" id="approveSpecsQty">-</span>
                    </div>
                    <div class="text-xs text-slate-800 font-medium bg-amber-50/70 p-2.5 rounded-lg border border-amber-200/80 whitespace-pre-line leading-relaxed min-h-[38px]" id="approveDeskripsi">
                        -
                    </div>
                </div>

                {{-- Deskripsi Narasi Penyebab NCR --}}
                <div class="border-t border-slate-200/80 pt-2 mt-2">
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wide flex items-center gap-1.5 mb-1">
                        <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <span>Deskripsi Narasi Penyebab (Dicetak ke Surat NCR PDF)</span>
                    </span>
                    <div class="text-xs text-slate-800 font-medium bg-sky-50/70 p-2.5 rounded-lg border border-sky-200/80 whitespace-pre-line leading-relaxed min-h-[38px]" id="approveDeskripsiPenyebab">
                        -
                    </div>
                </div>
            </div>

            {{-- FOKUS UTAMA: PENINJAUAN & VALIDASI CAPA --}}
            <div class="bg-purple-50/60 border-2 border-purple-200 rounded-xl p-4 space-y-3">
                <div class="flex items-center gap-2 border-b border-purple-200/80 pb-2">
                    <svg class="w-4 h-4 text-purple-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                    <h4 class="font-bold text-purple-900 text-xs uppercase tracking-wide">Pemeriksaan &amp; Validasi Tindakan (CAPA)</h4>
                    <span class="ml-auto text-[10px] bg-purple-200 text-purple-800 font-bold px-2 py-0.5 rounded">Tugas Utama SPV</span>
                </div>

                {{-- Corrective Action --}}
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.546 2.546 0 01-3.587 0 2.546 2.546 0 010-3.587l5.653-4.655m3.589-2.411a5.34 5.34 0 00-2.38-2.38m0 0l-3.03 2.496m5.41-2.496l1.246-1.246a2.25 2.25 0 013.182 0l2.122 2.122a2.25 2.25 0 010 3.182l-1.246 1.246"/>
                            </svg>
                            <span>Corrective Action (Tindakan Penanganan Langsung)</span>
                        </span>
                        <span class="text-[10px] font-normal text-slate-500">Penanggulangan cacat saat ini</span>
                    </label>
                    <textarea name="corrective_action" id="approveCorrectiveInput" rows="2"
                              placeholder="Usulan tindakan perbaikan dari QA..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:ring-purple-500 focus:border-purple-500 bg-white font-medium text-slate-800"></textarea>
                </div>

                {{-- Preventive Action --}}
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            <span>Preventive Action (Tindakan Pencegahan Ulang)</span>
                        </span>
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
                    <option value="Approved">Disetujui (Approved — CAPA Sah &amp; Form NCR Terbit)</option>
                    <option value="Pending">Pending (Membutuhkan Peninjauan/Data Tambahan)</option>
                    <option value="Rejected">Ditolak / Perlu Revisi CAPA oleh QA</option>
                </select>
            </div>

            {{-- Keterangan Penyelesaian — hanya wewenang SPV, muncul saat Approved --}}
            <div id="keteranganBoxSpv" class="bg-rose-50 border-2 border-rose-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                    <h4 class="font-bold text-rose-900 text-xs uppercase tracking-wide">Keterangan Penyelesaian Akhir</h4>
                    <span class="ml-auto text-[10px] bg-rose-200 text-rose-800 font-bold px-2 py-0.5 rounded">Wewenang SPV</span>
                </div>
                <p class="text-[11px] text-rose-700 mb-3">Tentukan keputusan akhir penanganan komplain ini: apakah barang dikembalikan (Retur) atau cukup diberikan masukan/klarifikasi (Feedback) kepada customer.</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 bg-white cursor-pointer hover:border-emerald-400 hover:bg-emerald-50 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                        <input type="radio" name="keterangan" value="Feedback" class="accent-emerald-600 w-4 h-4" checked>
                        <div>
                            <div class="font-bold text-emerald-700 text-xs flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Feedback</span>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">Masukan / klarifikasi ke customer, tanpa pengembalian barang</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 bg-white cursor-pointer hover:border-rose-400 hover:bg-rose-50 transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50">
                        <input type="radio" name="keterangan" value="Retur" class="accent-rose-600 w-4 h-4">
                        <div>
                            <div class="font-bold text-rose-700 text-xs flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                </svg>
                                <span>Retur</span>
                            </div>
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
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span>Jadwalkan Kunjungan Lapangan (Visit Customer)</span>
                    </span>
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
        type: 'bar',
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

function initSpvPareto() { buildParetoChart('spvP-jenis'); }
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSpvPareto);
} else {
    initSpvPareto();
}

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

// ── Handler Dropdown Rentang Tahun (Maksimal 10 Tahun) ────────────────────────
function onLaporanRangeChange(source) {
    const fromEl = document.getElementById('laporanFromSelect');
    const toEl   = document.getElementById('laporanToSelect');
    if (!fromEl || !toEl) return;

    let from = parseInt(fromEl.value, 10);
    let to   = parseInt(toEl.value, 10);
    const maxRange = {{ $maxRange }};

    if (source === 'from') {
        if (to < from) {
            to = from;
        } else if ((to - from + 1) > maxRange) {
            to = from + maxRange - 1;
        }
    } else if (source === 'to') {
        if (from > to) {
            from = to;
        } else if ((to - from + 1) > maxRange) {
            from = to - maxRange + 1;
        }
    }

    fromEl.value = from;
    toEl.value   = to;
    document.getElementById('laporanFilterForm')?.submit();
}

// ── Tren Komplain (Otomatis Bulanan jika 1 tahun, Tahunan jika rentang tahun) ──
const isSingleYearTrend = @json($isSingleYear);
const fromYearTrend     = @json($fromYear);
const toYearTrend       = @json($toYear);
const currentYearTrend  = @json($currentYear);
const trendLabelsData   = @json($trendLabels);
const trendValuesData   = @json($trendData);
const trendMonthlyPrev  = @json($trendMonthlyPrev);

const trendCanvas = document.getElementById('spvTrendChart');
if (trendCanvas) {
    if (isSingleYearTrend) {
        // Mode 1 Tahun: Menampilkan rincian Jan s/d Des
        const datasets = [
            {
                type: 'bar',
                label: 'Tahun ' + fromYearTrend,
                data: trendValuesData,
                backgroundColor: '#4e73df',
                borderRadius: 4,
                maxBarThickness: 34,
                order: 2
            }
        ];

        if (trendMonthlyPrev) {
            datasets.push({
                type: 'line',
                label: 'Periode sebelumnya (' + (fromYearTrend - 1) + ')',
                data: trendMonthlyPrev,
                borderColor: '#94a3b8',
                borderDash: [5, 4],
                borderWidth: 2,
                pointRadius: 2.5,
                pointBackgroundColor: '#94a3b8',
                tension: 0.35,
                fill: false,
                order: 1
            });
        }

        new Chart(trendCanvas, {
            data: {
                labels: trendLabelsData,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'start',
                        labels: { boxWidth: 12, font: { size: 11, weight: '600' } }
                    },
                    datalabels: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    } else {
        // Mode Rentang Tahun: Menampilkan perbandingan tahunan (maksimal 10 batang)
        const barColors = trendLabelsData.map(y => parseInt(y, 10) === currentYearTrend ? '#4e73df' : '#64748b');

        new Chart(trendCanvas, {
            type: 'bar',
            data: {
                labels: trendLabelsData,
                datasets: [{
                    label: 'Total komplain per tahun',
                    data: trendValuesData,
                    backgroundColor: barColors,
                    borderRadius: 6,
                    maxBarThickness: 56
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 24 } },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'start',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11, weight: '600' },
                            generateLabels: function() {
                                return [
                                    { text: 'Total komplain per tahun', fillStyle: '#64748b', strokeStyle: '#64748b', lineWidth: 0, hidden: false, index: 0 },
                                    { text: 'Tahun berjalan (' + currentYearTrend + ')', fillStyle: '#4e73df', strokeStyle: '#4e73df', lineWidth: 0, hidden: false, index: 1 }
                                ];
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'top',
                        color: '#1e293b',
                        font: { weight: 'bold', size: 11 },
                        formatter: (val) => val > 0 ? val : '0'
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false }, ticks: { font: { size: 12, weight: 'bold' } } }
                }
            }
        });
    }
}

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
    
    // Reset the submit button state in case it was disabled previously
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Simpan Validasi CAPA &amp; NCR';
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
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

@endsection
