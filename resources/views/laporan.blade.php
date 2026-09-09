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
        <select name="tahun" class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-28">
            @foreach($years as $y)
                <option value="{{ $y }}" @selected($y == $tahun)>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Bulan</label>
        <select name="bulan" class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-36">
            <option value="">Semua Bulan</option>
            @foreach(array_slice($bulanOptions,1) as $i => $bln)
                <option value="{{ $i+1 }}" @selected(($i+1) == $bulan)>{{ $bln }}</option>
            @endforeach
        </select>
    </div>
    <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-5 py-2 rounded-lg">Tampilkan</button>
    @if($bulan)
        <a href="{{ route('laporan', ['tahun' => $tahun]) }}" class="text-sm text-slate-500 hover:text-slate-700 px-2 py-2">Reset Bulan</a>
    @endif
    <div class="flex-1"></div>
    <a href="{{ route('export.laporan.pdf', request()->query()) }}" target="_blank"
       class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-5 py-2 rounded-lg shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Export PDF {{ request()->hasAny(['tahun','bulan']) ? '(Terfilter)' : '' }}
    </a>
</form>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    @php
        $kpis = [
            ['Total Complaint', $totalComplaint, 'bg-indigo-500', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
            ['Status Open', $totalOpen, 'bg-amber-500', 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Diproses QA', $totalDiproses, 'bg-sky-500', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Close (Selesai)', $totalClose, 'bg-emerald-500', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Total Qty NG', number_format($totalQty), 'bg-rose-500', 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['Avg Lead Time', $avgLeadTime.' hr', 'bg-violet-500', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
    @endphp
    @foreach($kpis as [$label, $val, $color, $icon])
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex flex-col gap-2">
        <div class="w-9 h-9 rounded-lg {{ $color }} flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
            </svg>
        </div>
        <div class="text-xl font-bold text-slate-800">{{ $val }}</div>
        <div class="text-[11px] text-slate-500 font-medium">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Charts Row 1 --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Trend Bulanan / Tahunan --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-700">📈 Trend Complaint {{ $tahun }}</h3>
            @if($bulan)<span class="text-xs bg-sky-100 text-sky-700 px-2 py-0.5 rounded font-medium">{{ $bulanOptions[$bulan] }}</span>@endif
        </div>
        <canvas id="trendChart" height="100"></canvas>
    </div>

    {{-- Status Pie --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-slate-700 mb-4">🥧 Distribusi Status</h3>
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
        <h3 class="text-sm font-bold text-slate-700 mb-4">🔴 Top Jenis Ketidaksesuaian (Pareto)</h3>
        @if($defectRaw->isEmpty())
            <p class="text-xs text-slate-400 text-center py-8">Belum ada data.</p>
        @else
            <canvas id="defectChart" height="200"></canvas>
        @endif
    </div>

    {{-- Top Penyebab --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-slate-700 mb-4">⚠️ Top Penyebab Ketidaksesuaian</h3>
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
        <h3 class="text-sm font-bold text-slate-700">🏆 Ranking Customer Berdasarkan Jumlah Komplain — {{ $tahun }}</h3>
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
                <span>🐟</span> Diagram Sebab-Akibat Spesifik (Fishbone / Ishikawa 6M)
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Penelusuran akar masalah berdasarkan Detail Cacat &amp; Detail Penyebab Spesifik 6M.</p>
        </div>

        <form method="GET" action="{{ route('laporan') }}#fishbone-section" class="flex flex-wrap items-center gap-2">
            @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif
            @if(request('bulan')) <input type="hidden" name="bulan" value="{{ request('bulan') }}"> @endif

            <select name="fishbone_efek" onchange="this.form.submit()" class="bg-slate-50 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded-xl border border-slate-300">
                <option value="AUTO" {{ ($fishbone['selected_efek'] ?? 'AUTO') === 'AUTO' ? 'selected' : '' }}>
                    ⚡ Auto Pareto (Kasus Tertinggi: {{ $fishbone['top_efek'] ?? '-' }})
                </option>
                <option value="ALL" {{ ($fishbone['selected_efek'] ?? '') === 'ALL' ? 'selected' : '' }}>
                    🌐 Semua Masalah / Global
                </option>
                @if(!empty($fishbone['available_jenis']))
                    <optgroup label="📌 Jenis Ketidaksesuaian">
                        @foreach ($fishbone['available_jenis'] as $jOpt)
                            <option value="{{ $jOpt }}" {{ ($fishbone['selected_efek'] ?? '') === $jOpt ? 'selected' : '' }}>🏷️ {{ $jOpt }}</option>
                        @endforeach
                    </optgroup>
                @endif
                @if(!empty($fishbone['available_detail']))
                    <optgroup label="🔍 Detail Ketidaksesuaian Spesifik">
                        @foreach ($fishbone['available_detail'] as $dOpt)
                            <option value="{{ $dOpt }}" {{ ($fishbone['selected_efek'] ?? '') === $dOpt ? 'selected' : '' }}>🔍 {{ $dOpt }}</option>
                        @endforeach
                    </optgroup>
                @endif
            </select>

            <button type="submit" name="fishbone_level" value="detail" class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ ($fishbone['level'] ?? 'detail') === 'detail' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                🔍 Detail Spesifik
            </button>
            <button type="submit" name="fishbone_level" value="general" class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ ($fishbone['level'] ?? '') === 'general' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                📊 Kategori Umum
            </button>
        </form>
    </div>

    {{-- FISHBONE SKELETON --}}
    <div class="overflow-x-auto pb-2">
        <div class="min-w-[960px] bg-slate-950 text-white rounded-3xl p-6 relative border border-slate-800 shadow-xl overflow-hidden">
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-400 animate-pulse"></span>
                    <h4 class="text-xs font-mono font-bold uppercase tracking-widest text-sky-400">DIAGRAM SEBAB-AKIBAT ISHIKAWA 6M</h4>
                </div>
                <div class="text-xs font-semibold text-sky-200 bg-sky-950 px-3 py-1 rounded-full border border-sky-800">
                    Fokus Akibat: <strong class="text-white">{{ $fishbone['efek'] }}</strong>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-6 items-center relative py-2">
                <div class="col-span-9 space-y-6">
                    {{-- UPPER BRANCHES --}}
                    <div class="grid grid-cols-3 gap-4">
                        @foreach (['Man', 'Material', 'Environment'] as $catKey)
                            @php $cData = collect($fishbone['categories'])->firstWhere('kategori', $catKey); @endphp
                            <div class="bg-slate-900 border border-sky-500/30 rounded-2xl p-4 shadow text-white flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-800">
                                        <span class="font-bold text-xs text-sky-400 font-mono flex items-center gap-1.5 uppercase">
                                            <span class="w-2 h-2 rounded-full bg-sky-400"></span> {{ $catKey }}
                                        </span>
                                        <span class="text-[10px] bg-sky-950 text-sky-300 font-mono px-2 py-0.5 rounded-full border border-sky-800 font-bold">
                                            {{ $cData['total'] ?? 0 }} isu
                                        </span>
                                    </div>
                                    <ul class="space-y-1.5">
                                        @forelse ($cData['top_causes'] ?? [] as $c)
                                            <li>
                                                <button type="button" onclick='showFishboneModal(@json($c), "{{ $catKey }}")' class="w-full text-left p-2 rounded-xl text-xs flex items-center justify-between transition {{ $loop->first ? 'bg-sky-500/20 border border-sky-400 text-white font-bold' : 'bg-slate-800/90 text-slate-200 hover:bg-slate-700' }}">
                                                    <span class="whitespace-normal break-words leading-tight flex-1 pr-1">{{ $c['nama'] }}</span>
                                                    <span class="text-[10px] font-bold text-sky-300 bg-slate-900 px-1.5 py-0.5 rounded font-mono shrink-0">{{ $c['jumlah'] }}</span>
                                                </button>
                                            </li>
                                        @empty
                                            <li class="text-[11px] text-slate-500 italic text-center py-2">Tidak ada isu</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- CENTER SPINE LINE --}}
                    <div class="relative py-2 flex items-center">
                        <div class="w-full h-2 bg-gradient-to-r from-slate-700 via-sky-500 to-sky-400 rounded-full shadow"></div>
                        <div class="text-sky-400 -ml-1">
                            <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>

                    {{-- LOWER BRANCHES --}}
                    <div class="grid grid-cols-3 gap-4">
                        @foreach (['Machine', 'Method', 'Measurement'] as $catKey)
                            @php $cData = collect($fishbone['categories'])->firstWhere('kategori', $catKey); @endphp
                            <div class="bg-slate-900 border border-indigo-500/30 rounded-2xl p-4 shadow text-white flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-800">
                                        <span class="font-bold text-xs text-indigo-400 font-mono flex items-center gap-1.5 uppercase">
                                            <span class="w-2 h-2 rounded-full bg-indigo-400"></span> {{ $catKey }}
                                        </span>
                                        <span class="text-[10px] bg-indigo-950 text-indigo-300 font-mono px-2 py-0.5 rounded-full border border-indigo-800 font-bold">
                                            {{ $cData['total'] ?? 0 }} isu
                                        </span>
                                    </div>
                                    <ul class="space-y-1.5">
                                        @forelse ($cData['top_causes'] ?? [] as $c)
                                            <li>
                                                <button type="button" onclick='showFishboneModal(@json($c), "{{ $catKey }}")' class="w-full text-left p-2 rounded-xl text-xs flex items-center justify-between transition {{ $loop->first ? 'bg-indigo-500/20 border border-indigo-400 text-white font-bold' : 'bg-slate-800/90 text-slate-200 hover:bg-slate-700' }}">
                                                    <span class="whitespace-normal break-words leading-tight flex-1 pr-1">{{ $c['nama'] }}</span>
                                                    <span class="text-[10px] font-bold text-indigo-300 bg-slate-900 px-1.5 py-0.5 rounded font-mono shrink-0">{{ $c['jumlah'] }}</span>
                                                </button>
                                            </li>
                                        @empty
                                            <li class="text-[11px] text-slate-500 italic text-center py-2">Tidak ada isu</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- FISH HEAD --}}
                <div class="col-span-3 pl-2">
                    <div class="bg-gradient-to-b from-slate-900 via-slate-900 to-sky-950 text-white p-5 rounded-3xl shadow-xl text-center border-2 border-sky-400">
                        <div class="text-[10px] font-mono tracking-widest text-sky-400 font-bold uppercase mb-1">AKIBAT (PROBLEM HEAD)</div>
                        <div class="text-sm font-black text-white leading-snug break-words my-2">
                            {{ $fishbone['efek'] }}
                        </div>
                        <div class="mt-3 inline-block bg-sky-950 text-sky-300 border border-sky-800 text-[10px] font-bold font-mono px-3 py-1 rounded-full">
                            Total 6M Isu: {{ array_sum(array_column($fishbone['categories'], 'total')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- GRAFIK APRIORI (LIFT RATIO) --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="font-bold text-slate-800 text-sm">📊 Grafik Pola Keterkaitan Cacat Spesifik &amp; Penyebab (Lift Ratio — Apriori)</h3>
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

{{-- MODAL LAPORAN PENELUSURAN MASALAH FISHBONE --}}
<div id="fishboneDetailModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full border border-slate-200 overflow-hidden my-8">
        <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-3">
                <span id="modalCategoryBadge" class="bg-sky-500 text-white text-xs font-mono font-bold px-3 py-1 rounded-full uppercase">MACHINE</span>
                <div>
                    <h3 class="font-bold text-base text-white">Laporan Penelusuran Masalah Spesifik</h3>
                    <p class="text-xs text-sky-300">Penyebab: <strong id="modalCauseTitle" class="text-white">-</strong></p>
                </div>
            </div>
            <button type="button" onclick="closeFishboneModal()" class="text-slate-400 hover:text-white transition p-1 rounded-lg hover:bg-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            <div class="flex items-center justify-between text-xs text-slate-500 pb-2 border-b border-slate-100">
                <span>Daftar Transaksi Komplain &amp; Tindakan Perbaikan Lengkap</span>
                <span id="modalTotalCount" class="font-bold text-slate-800 font-mono">Total 0 Komplain</span>
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-xl shadow-sm">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-mono font-bold text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-3">No. Customer &amp; Name</th>
                            <th class="px-3 py-3">Tanggal &amp; Area</th>
                            <th class="px-3 py-3">Cacat Spesifik</th>
                            <th class="px-3 py-3 text-right">Qty</th>
                            <th class="px-3 py-3">Tindakan Perbaikan (CAPA)</th>
                        </tr>
                    </thead>
                    <tbody id="modalTableBody" class="divide-y divide-slate-100 text-slate-700">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex items-center justify-end">
            <button type="button" onclick="closeFishboneModal()" class="px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-xl hover:bg-slate-900 transition">
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

// --- Fishbone Detail Modal JS ---
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
        tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-slate-400 italic">Tidak ada rincian data komplain untuk penyebab ini.</td></tr>`;
    } else {
        tbody.innerHTML = complaints.map(c => `
            <tr class="hover:bg-sky-50/50 transition">
                <td class="px-3 py-3 font-medium">
                    <div class="font-bold text-slate-900">${c.no_customer || '-'}</div>
                    <div class="text-sky-600 font-semibold">${c.nama_customer || '-'}</div>
                </td>
                <td class="px-3 py-3">
                    <div>📅 ${c.tanggal_complain || '-'}</div>
                    <div class="text-[11px] text-slate-500 font-mono">📍 ${c.area || '-'}</div>
                </td>
                <td class="px-3 py-3">
                    <span class="inline-block bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded font-mono">${c.jenis_ketidaksesuaian || '-'}</span>
                    <div class="text-slate-600 mt-1 font-medium">${c.detail_ketidaksesuaian || '-'}</div>
                </td>
                <td class="px-3 py-3 text-right font-mono font-bold text-slate-900">
                    ${c.qty ? c.qty.toLocaleString() : '-'}
                </td>
                <td class="px-3 py-3 space-y-1 text-[11px]">
                    <div class="bg-emerald-50 text-emerald-900 p-2 rounded border border-emerald-200/60">
                        <strong class="text-emerald-700">Corrective:</strong> ${c.corrective_action || '-'}
                    </div>
                    <div class="bg-blue-50 text-blue-900 p-2 rounded border border-blue-200/60">
                        <strong class="text-blue-700">Preventive:</strong> ${c.preventive_action || '-'}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    modal.classList.remove('hidden');
}

function closeFishboneModal() {
    const modal = document.getElementById('fishboneDetailModal');
    if (modal) modal.classList.add('hidden');
}
</script>
@endpush
