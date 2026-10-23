@extends('layouts.app')
@section('title', 'Laporan Visual')
@section('subtitle', 'Ringkasan & Visualisasi Data Complaint · PT Wahana Bermuda Nusantara')

@section('content')
@php
    $bulanOptions = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

{{-- Filter --}}
<form method="GET" class="bg-white rounded-xl border border-slate-200 shadow-sm px-6 py-4 mb-6 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Tahun</label>
        <select name="tahun" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs h-9 w-28 bg-white font-semibold">
            @foreach($years as $y)
                <option value="{{ $y }}" @selected($y == $tahun)>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Bulan</label>
        <select name="bulan" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs h-9 w-36 bg-white font-semibold">
            <option value="">Semua Bulan</option>
            @foreach(array_slice($bulanOptions,1) as $i => $bln)
                <option value="{{ $i+1 }}" @selected(($i+1) == $bulan)>{{ $bln }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary btn-sm h-9">
        <i class="fas fa-filter"></i>
        <span>Tampilkan</span>
    </button>
    @if($bulan)
        <a href="{{ route('laporan', ['tahun' => $tahun]) }}" class="btn btn-outline-secondary btn-sm h-9">
            <i class="fas fa-undo"></i>
            <span>Reset Bulan</span>
        </a>
    @endif
    <div class="flex-1"></div>
    <a href="{{ route('export.laporan.pdf', request()->query()) }}" target="_blank"
       class="btn btn-danger btn-sm h-9">
        <i class="fas fa-file-pdf"></i>
        <span>Export PDF {{ request()->hasAny(['tahun','bulan']) ? '(Terfilter)' : '' }}</span>
    </a>
</form>

{{-- KPI Cards (SB Admin 2 Style) --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    @php
        $kpis = [
            ['Total Complaint', $totalComplaint, 'border-left-primary', 'text-[#4e73df]', 'fa-clipboard-list'],
            ['Status Open', $totalOpen, 'border-left-warning', 'text-[#f6c23e]', 'fa-folder-open'],
            ['Diproses QA', $totalDiproses, 'border-left-info', 'text-[#36b9cc]', 'fa-spinner'],
            ['Close (Selesai)', $totalClose, 'border-left-success', 'text-[#1cc88a]', 'fa-check-circle'],
            ['Total Qty NG', number_format($totalQty), 'border-left-danger', 'text-[#e74a3b]', 'fa-boxes'],
            ['Avg Lead Time', $avgLeadTime.' hr', 'border-left-purple', 'text-[#6f42c1]', 'fa-stopwatch'],
        ];
    @endphp
    @foreach($kpis as [$label, $val, $borderClass, $textClass, $icon])
    <div class="bg-white rounded shadow-sm border-l-4 {{ $borderClass }} p-3.5 flex items-center justify-between">
        <div class="min-w-0 pr-1">
            <div class="text-[10px] font-extrabold uppercase tracking-wider {{ $textClass }} mb-1 truncate">{{ $label }}</div>
            <div class="text-lg md:text-xl font-black text-gray-800 leading-tight truncate">{{ $val }}</div>
        </div>
        <div class="shrink-0 text-gray-300">
            <i class="fas {{ $icon }} text-xl md:text-2xl"></i>
        </div>
    </div>
    @endforeach
</div>

{{-- Charts Row 1 --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Trend Bulanan / Tahunan --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-700 flex items-center gap-1.5"><x-icon name="chart-bar" class="w-4 h-4"/> Trend Complaint {{ $tahun }}</h3>
            @if($bulan)<span class="text-xs bg-sky-100 text-sky-700 px-2 py-0.5 rounded font-medium">{{ $bulanOptions[$bulan] }}</span>@endif
        </div>
        <canvas id="trendChart" height="100"></canvas>
    </div>

    {{-- Status Pie --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-1.5"><x-icon name="chart-pie" class="w-4 h-4"/> Distribusi Status</h3>
        <canvas id="statusChart" height="180"></canvas>
        <div class="mt-4 space-y-1.5">
            @foreach(['Open' => ['bg-amber-400', $totalOpen], 'Diproses' => ['bg-sky-400', $totalDiproses], 'Close' => ['bg-emerald-400', $totalClose]] as $s => [$cls, $n])
            <div class="flex items-center justify-between text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm {{ $cls }}"></span>{{ $s }}</span>
                <span class="font-semibold text-slate-700">{{ $n }} <span class="text-slate-400 font-normal">({{ $totalComplaint ? round($n/$totalComplaint*100) : 0 }}%)</span></span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Charts Row 2 --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    {{-- Top Defect --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-1.5"><x-icon name="exclamation-circle" class="w-4 h-4 text-rose-500"/> Top Jenis Ketidaksesuaian (Pareto)</h3>
        @if($defectRaw->isEmpty())
            <p class="text-xs text-slate-400 text-center py-8">Belum ada data.</p>
        @else
            <canvas id="defectChart" height="200"></canvas>
        @endif
    </div>

    {{-- Top Penyebab --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-1.5"><x-icon name="exclamation-triangle" class="w-4 h-4 text-amber-500"/> Top Penyebab Ketidaksesuaian</h3>
        @if($penyebabRaw->isEmpty())
            <p class="text-xs text-slate-400 text-center py-8">Belum ada data.</p>
        @else
            <canvas id="penyebabChart" height="200"></canvas>
        @endif
    </div>
</div>

{{-- Top Customer Table --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-5">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-700 flex items-center gap-1.5"><x-icon name="trophy" class="w-4 h-4 text-amber-500"/> Ranking Customer Berdasarkan Jumlah Komplain — {{ $tahun }}</h3>
        <span class="text-xs text-slate-400">Top 10</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs uppercase text-slate-500 bg-slate-50">
                    <th class="px-5 py-3 text-left">No</th>
                    <th class="px-5 py-3 text-left">Customer</th>
                    <th class="px-5 py-3 text-center">Jumlah Kasus</th>
                    <th class="px-5 py-3 text-right">Total Qty NG</th>
                    <th class="px-5 py-3 text-left">Proporsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($custRaw as $i => $c)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 text-slate-400 text-xs">{{ $i+1 }}</td>
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $c->nama_customer }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">{{ $c->total }}</span>
                    </td>
                    <td class="px-5 py-3 text-right text-rose-600 font-medium text-xs">{{ number_format($c->total_qty) }}</td>
                    <td class="px-5 py-3">
                        @php $pct = $totalComplaint ? round($c->total / $totalComplaint * 100) : 0; @endphp
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                                <div class="bg-indigo-500 h-1.5 rounded-full" style="width:{{ $pct }}%"></div>
                            </div>
                            <span class="text-xs text-slate-500 w-8 text-right">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- FISHBONE / ISHIKAWA 6M (SPESIFIK DETAIL CACAT & DETAIL PENYEBAB) --}}
<section id="fishbone-section" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <x-icon name="chart-bar" class="w-5 h-5"/> Diagram Sebab-Akibat Spesifik (Fishbone / Ishikawa 6M)
            </h3>
        </div>
    </div>

    <!-- ANALISIS SEBAB-AKIBAT ISHIKAWA 6M (SB ADMIN 2 CARD GRID STYLE) -->
    <div class="card-sb mb-6 border border-[#e3e6f0] rounded shadow-sm overflow-hidden">
        <!-- Header Rapi & Kompak: Judul + Filter Inline (Tanpa Box Bertumpuk) -->
        <div class="card-sb-header py-3 px-4 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-sitemap text-[#4e73df] text-lg"></i>
                <div>
                    <h6 class="m-0 font-bold text-[#4e73df] text-sm leading-tight">DIAGRAM SEBAB-AKIBAT ISHIKAWA (6M)</h6>
                    <small class="text-xs text-[#858796] block mt-0.5">Pilih salah satu penyebab di bawah untuk menyorot tindakan perbaikan (CAPA)</small>
                </div>
            </div>

            <!-- Form Filter Ringkas Inline (Hanya 1 baris di header) -->
            <form method="GET" action="{{ route('laporan') }}#fishbone-section" class="flex flex-wrap items-center gap-2 m-0">
                @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif
                @if(request('bulan')) <input type="hidden" name="bulan" value="{{ request('bulan') }}"> @endif

                <div class="flex items-center gap-1.5">
                    <span class="text-xs text-[#858796] font-semibold">Akibat:</span>
                    <select name="fishbone_efek" id="fishbone_efek_lap" onchange="this.form.submit()" class="text-xs font-semibold px-2.5 py-1 rounded border border-[#d1d3e2] bg-white text-[#5a5c69] focus:outline-none focus:border-[#4e73df] cursor-pointer shadow-none max-w-[220px]">
                        <option value="AUTO" {{ ($fishbone['selected_efek'] ?? 'AUTO') === 'AUTO' ? 'selected' : '' }}>
                            Auto: {{ $fishbone['top_efek'] ?? 'Tertinggi' }}
                        </option>
                        <option value="ALL" {{ ($fishbone['selected_efek'] ?? '') === 'ALL' ? 'selected' : '' }}>
                            Semua Masalah (Global)
                        </option>
                        @if(!empty($fishbone['available_jenis']))
                            <optgroup label="Jenis Ketidaksesuaian">
                                @foreach ($fishbone['available_jenis'] as $jOpt)
                                    <option value="{{ $jOpt }}" {{ ($fishbone['selected_efek'] ?? '') === $jOpt ? 'selected' : '' }}>{{ $jOpt }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                        @if(!empty($fishbone['available_detail']))
                            <optgroup label="Detail Ketidaksesuaian Spesifik">
                                @foreach ($fishbone['available_detail'] as $dOpt)
                                    <option value="{{ $dOpt }}" {{ ($fishbone['selected_efek'] ?? '') === $dOpt ? 'selected' : '' }}>{{ $dOpt }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>

                <input type="hidden" name="fishbone_level" value="detail">

                <span class="badge badge-primary font-bold text-xs px-2.5 py-1">
                    {{ array_sum(array_column($fishbone['categories'], 'total')) }} Isu
                </span>
            </form>
        </div>

        <!-- Body: Grid 6 Kategori (3 Kolom pada Desktop, 2 pada Tablet, 1 pada HP) -->
        <div class="p-4 bg-[#f8f9fc]">
            @php
                $categoriesConfig = [
                    'Man' => [
                        'title' => 'Man (Tenaga Kerja)',
                        'border' => 'border-left-primary',
                        'text_color' => 'text-[#4e73df]',
                        'icon' => 'fa-user',
                        'badge' => 'badge-primary'
                    ],
                    'Material' => [
                        'title' => 'Material (Bahan Baku)',
                        'border' => 'border-left-success',
                        'text_color' => 'text-[#1cc88a]',
                        'icon' => 'fa-boxes',
                        'badge' => 'badge-success'
                    ],
                    'Machine' => [
                        'title' => 'Machine (Mesin & Alat)',
                        'border' => 'border-left-warning',
                        'text_color' => 'text-[#f6c23e]',
                        'icon' => 'fa-cogs',
                        'badge' => 'badge-warning text-dark'
                    ],
                    'Method' => [
                        'title' => 'Method (Metode Kerja)',
                        'border' => 'border-left-danger',
                        'text_color' => 'text-[#e74a3b]',
                        'icon' => 'fa-tasks',
                        'badge' => 'badge-danger'
                    ],
                    'Measurement' => [
                        'title' => 'Measurement (Pengukuran)',
                        'border' => 'border-left-purple',
                        'text_color' => 'text-[#6f42c1]',
                        'icon' => 'fa-ruler-combined',
                        'badge' => 'badge-purple'
                    ],
                    'Environment' => [
                        'title' => 'Environment (Lingkungan)',
                        'border' => 'border-left-info',
                        'text_color' => 'text-[#36b9cc]',
                        'icon' => 'fa-leaf',
                        'badge' => 'badge-info'
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($categoriesConfig as $catKey => $cfg)
                    @php $cData = collect($fishbone['categories'])->firstWhere('kategori', $catKey); @endphp
                    <div class="bg-white border border-[#e3e6f0] {{ $cfg['border'] }} shadow-sm rounded flex flex-col justify-between h-full transition hover:shadow">
                        <!-- Category Header -->
                        <div class="px-4 py-2.5 border-b border-[#e3e6f0] flex items-center justify-between bg-white">
                            <div class="flex items-center gap-2">
                                <i class="fas {{ $cfg['icon'] }} {{ $cfg['text_color'] }} text-sm"></i>
                                <span class="font-bold text-xs uppercase tracking-wide {{ $cfg['text_color'] }}">
                                    {{ $cfg['title'] }}
                                </span>
                            </div>
                            <span class="badge {{ ($cData['total'] ?? 0) > 0 ? $cfg['badge'] : 'badge-light border text-[#858796]' }} font-bold text-[11px] px-2 py-0.5">
                                {{ $cData['total'] ?? 0 }} isu
                            </span>
                        </div>

                        <!-- Causes List (Klik untuk Sorot Penyebab + CAPA) -->
                        <div class="p-3 flex-1 flex flex-col justify-between">
                            <ul class="space-y-2">
                                @forelse ($cData['top_causes'] ?? [] as $c)
                                    <li>
                                        <button type="button" 
                                                onclick='showFishboneModal(@json($c), "{{ $catKey }}")'
                                                class="w-full text-left p-2.5 rounded border border-[#e3e6f0] bg-white hover:bg-[#f8f9fc] hover:border-[#4e73df] flex items-center justify-between transition group cursor-pointer">
                                            <div class="flex-1 pr-2">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    @if($loop->first && ($c['jumlah'] ?? 0) > 0)
                                                        <span class="badge badge-warning text-dark text-[9px] px-1.5 py-0.5 font-bold">
                                                            <i class="fas fa-fire mr-0.5"></i>Top
                                                        </span>
                                                    @endif
                                                    <span class="text-xs text-[#5a5c69] font-medium leading-snug group-hover:text-[#4e73df] transition">
                                                        {{ $c['nama'] }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="badge badge-light border border-[#d1d3e2] text-[#4e73df] font-bold text-xs px-2 py-1 shrink-0">
                                                {{ $c['jumlah'] }}
                                            </span>
                                        </button>
                                    </li>
                                @empty
                                    <li class="py-5 text-center">
                                        <i class="fas fa-check-circle text-emerald-400 text-sm mb-1 block"></i>
                                        <span class="text-xs text-[#858796] italic">Tidak ada isu terdeteksi</span>
                                    </li>
                                @endforelse
                            </ul>

                            @if(($cData['other_count'] ?? 0) > 0)
                                <div class="mt-3 pt-2.5 border-t border-[#e3e6f0] text-center">
                                    <button type="button" onclick='showFishboneCategoryAllModal(@json($cData), "{{ $catKey }}")' class="text-xs font-bold text-[#4e73df] hover:underline flex items-center justify-center gap-1.5 w-full py-1">
                                        <i class="fas fa-list-ul"></i>
                                        <span>Lihat +{{ $cData['other_count'] }} penyebab lainnya</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- GRAFIK APRIORI (LIFT RATIO) --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5"><x-icon name="share" class="w-4 h-4"/> Grafik Pola Keterkaitan Cacat Spesifik &amp; Penyebab (Lift Ratio — Apriori)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Tingkat kekuatan kaitan sebab-akibat antara jenis ketidaksesuaian dan penyebab utama.</p>
        </div>
        <a href="{{ route('apriori') }}" class="text-xs text-sky-600 font-medium hover:underline whitespace-nowrap">Analisis lengkap →</a>
    </div>

    @if(empty($topRules))
        <p class="text-sm text-slate-400 py-8 text-center">Belum ada pola Apriori. Tambah data complaint untuk melihat hasil.</p>
    @else
        <div class="relative h-60 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
            <canvas id="laporanAprioriChart"></canvas>
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

{{-- MODAL LAPORAN PENELUSURAN MASALAH SPESIFIK (SB ADMIN 2 THEME) --}}
<div id="fishboneDetailModal"
     class="fixed inset-0 !m-0 z-[9999] hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
     onclick="if (event.target === this) closeFishboneModal()">
    <div class="bg-white rounded max-w-4xl w-full border border-[#e3e6f0] shadow-2xl overflow-hidden transform transition-all my-8"
         onclick="event.stopPropagation()">
        <!-- Modal Header SB Admin 2 -->
        <div class="bg-[#f8f9fc] text-[#5a5c69] px-4 py-3 flex items-center justify-between border-b border-[#e3e6f0]">
            <div class="flex items-center gap-2.5">
                <span id="modalCategoryBadge" class="badge badge-primary uppercase font-bold text-xs px-2.5 py-1">MACHINE</span>
                <div>
                    <h6 class="font-bold text-sm text-[#4e73df] m-0 flex items-center gap-1.5">
                        <i class="fas fa-list-alt"></i>
                        <span>Laporan Penelusuran Masalah Spesifik</span>
                    </h6>
                    <small class="text-xs text-[#858796]">Penyebab: <strong id="modalCauseTitle" class="text-[#5a5c69]">-</strong></small>
                </div>
            </div>
            <button type="button" onclick="closeFishboneModal()" class="text-[#858796] hover:text-[#5a5c69] text-2xl font-bold leading-none px-2">&times;</button>
        </div>

        <!-- Modal Content -->
        <div class="p-4 space-y-3 max-h-[70vh] overflow-y-auto">
            <div class="flex items-center justify-between text-xs text-[#858796] pb-2 border-b border-[#e3e6f0]">
                <span>Daftar Transaksi Komplain &amp; Tindakan Perbaikan Lengkap</span>
                <span id="modalTotalCount" class="badge badge-light border font-bold text-[#5a5c69]">Total 0 Komplain</span>
            </div>

            <div class="overflow-x-auto border border-[#e3e6f0] rounded">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#f8f9fc] text-[#5a5c69] uppercase font-bold text-[11px] border-b border-[#e3e6f0]">
                        <tr>
                            <th class="px-3 py-2.5">No. NCR &amp; Customer</th>
                            <th class="px-3 py-2.5">Tanggal &amp; Area</th>
                            <th class="px-3 py-2.5">Cacat Spesifik</th>
                            <th class="px-3 py-2.5 text-right">Qty</th>
                            <th class="px-3 py-2.5">Tindakan Perbaikan (CAPA)</th>
                        </tr>
                    </thead>
                    <tbody id="modalTableBody" class="divide-y divide-[#e3e6f0] text-[#5a5c69]">
                        <!-- Populated dynamically via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-[#f8f9fc] px-4 py-2.5 border-t border-[#e3e6f0] flex items-center justify-end">
            <button type="button" onclick="closeFishboneModal()" class="btn btn-secondary btn-sm">
                Tutup Laporan
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const COLORS = ['#6366f1','#f59e0b','#10b981','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6'];

// Trend Chart
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: @json($trendLabels),
        datasets: [{
            label: 'Complaint',
            data: @json($trendData),
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#6366f1',
            pointRadius: 4,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

// Status Pie
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Open', 'Diproses', 'Close'],
        datasets: [{
            data: [{{ $statusData['Open'] }}, {{ $statusData['Diproses'] }}, {{ $statusData['Close'] }}],
            backgroundColor: ['#fbbf24','#38bdf8','#34d399'],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { display: false } }
    }
});

@if($defectRaw->isNotEmpty())
// Defect Pareto
new Chart(document.getElementById('defectChart'), {
    type: 'bar',
    data: {
        labels: @json($defectRaw->pluck('jenis_ketidaksesuaian')),
        datasets: [{
            label: 'Kasus',
            data: @json($defectRaw->pluck('total')),
            backgroundColor: COLORS,
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            y: { grid: { display: false } }
        }
    }
});
@endif

@if($penyebabRaw->isNotEmpty())
// Penyebab Chart
new Chart(document.getElementById('penyebabChart'), {
    type: 'bar',
    data: {
        labels: @json($penyebabRaw->pluck('penyebab')),
        datasets: [{
            label: 'Kasus',
            data: @json($penyebabRaw->pluck('total')),
            backgroundColor: COLORS.slice(2),
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            y: { grid: { display: false } }
        }
    }
});
@endif

// --- Apriori Chart ---
const topRules = @json($topRules);
const aprioriCanvas = document.getElementById('laporanAprioriChart');
if (aprioriCanvas && topRules.length > 0) {
    const labels = topRules.map(r => r.antecedents + ' ➔ ' + r.consequents);
    const dataLift = topRules.map(r => r.lift);
    const bgColors = topRules.map(r => {
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
                x: { beginAtZero: true, title: { display: true, text: 'Lift Ratio', font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                y: { grid: { display: false }, ticks: { font: { size: 10, weight: '600' } } }
            }
        }
    });
}

// --- Fishbone Detail Modal JS (SB Admin 2) ---
function showFishboneModal(causeData, categoryName) {
    const modal = document.getElementById('fishboneDetailModal');
    const badge = document.getElementById('modalCategoryBadge');
    const title = document.getElementById('modalCauseTitle');
    const total = document.getElementById('modalTotalCount');
    const tbody = document.getElementById('modalTableBody');

    if (!modal || !causeData) return;

    badge.innerText = categoryName;
    title.innerText = causeData.nama || '-';
    const complaints = causeData.complaints || [];
    total.innerText = `Total ${complaints.length} Kasus Komplain`;

    if (complaints.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-[#858796] italic">Tidak ada rincian data komplain untuk penyebab ini.</td></tr>`;
    } else {
        tbody.innerHTML = complaints.map(c => `
            <tr class="hover:bg-slate-50 transition">
                <td class="px-3 py-2.5 font-medium">
                    <div class="font-bold text-[#4e73df]">${c.no_customer || '-'}</div>
                    <div class="text-[#5a5c69] font-semibold">${c.nama_customer || '-'}</div>
                </td>
                <td class="px-3 py-2.5 text-[#5a5c69]">
                    <div class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-[#858796]"></i> <span>${c.tanggal_complain || '-'}</span></div>
                    <div class="text-[11px] text-[#858796] flex items-center gap-1.5 mt-0.5"><i class="fas fa-map-marker-alt text-[#858796]"></i> <span>${c.area || '-'}</span></div>
                </td>
                <td class="px-3 py-2.5">
                    <span class="badge badge-warning text-dark text-[10px] font-bold">${c.jenis_ketidaksesuaian || '-'}</span>
                    <div class="text-[#5a5c69] mt-1 font-medium">${c.detail_ketidaksesuaian || '-'}</div>
                </td>
                <td class="px-3 py-2.5 text-right font-bold text-[#5a5c69]">
                    ${c.qty ? c.qty.toLocaleString() : '-'}
                </td>
                <td class="px-3 py-2.5 space-y-1 text-[11px]">
                    <div class="bg-emerald-50 text-emerald-900 p-2 rounded border border-emerald-200">
                        <strong class="text-emerald-700">Corrective:</strong> ${c.corrective_action || '-'}
                    </div>
                    <div class="bg-blue-50 text-blue-900 p-2 rounded border border-blue-200">
                        <strong class="text-blue-700">Preventive:</strong> ${c.preventive_action || '-'}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function showFishboneCategoryAllModal(cData, categoryName) {
    if (!cData || !cData.causes) return;
    let allComplaints = [];
    cData.causes.forEach(c => {
        if (c.complaints) allComplaints = allComplaints.concat(c.complaints);
    });
    const aggregatedCause = {
        nama: `Seluruh Isu Kategori ${categoryName}`,
        complaints: allComplaints
    };
    showFishboneModal(aggregatedCause, categoryName);
}

function closeFishboneModal() {
    const modal = document.getElementById('fishboneDetailModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Tutup modal otomatis saat klik area latar belakang (luar dialog) atau tekan Escape
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('fishboneDetailModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeFishboneModal();
            }
        });
    }
});
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFishboneModal();
    }
});
</script>
@endpush
