@extends('layouts.app')
@section('title', 'Seven Tools Quality Control')
@section('subtitle', 'Analisis pengendalian kualitas dengan 7 alat bantu statistik')

@section('content')
@php
    function tool_header($no, $title, $desc) {
        return "<div class='flex items-start gap-3 mb-4'>
            <span class='shrink-0 w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold text-sm'>{$no}</span>
            <div><h2 class='font-semibold text-slate-800'>{$title}</h2><p class='text-xs text-slate-500'>{$desc}</p></div>
        </div>";
    }
@endphp

<!-- 1. CHECK SHEET -->
<section class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
    {!! tool_header(1, 'Check Sheet (Lembar Pemeriksaan)', 'Tabulasi frekuensi jenis ketidaksesuaian per bulan.') !!}

    {{-- Mode CheckSheet --}}
    <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2 mb-4 text-xs">
        {{-- Preserve all other filter params --}}
        @foreach (['pareto_year','pareto_month','pareto_detail_year','pareto_detail_month','pareto_cust_year','pareto_cust_month','pareto_cause_year','pareto_cause_month'] as $p)
            @if (request($p))<input type="hidden" name="{{ $p }}" value="{{ request($p) }}">@endif
        @endforeach

        <span class="text-slate-500">Tampilkan berdasarkan:</span>
        <select name="cs_mode" onchange="this.form.submit()"
                class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:ring-sky-500 focus:border-sky-500 font-medium">
            <option value="jenis_ketidaksesuaian" @selected($csMode === 'jenis_ketidaksesuaian')>Jenis Ketidaksesuaian</option>
            <option value="detail_ketidaksesuaian" @selected($csMode === 'detail_ketidaksesuaian')>Detail Ketidaksesuaian</option>
        </select>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-xs border-collapse">
            <thead>
                <tr class="bg-slate-100 text-slate-600">
                    <th class="border border-slate-200 px-2 py-2 text-left sticky left-0 bg-slate-100">
                        {{ $csMode === 'detail_ketidaksesuaian' ? 'Detail Ketidaksesuaian' : 'Jenis Ketidaksesuaian' }}
                    </th>
                    @foreach ($checkSheet['bulan'] as $b)
                        <th class="border border-slate-200 px-2 py-2 whitespace-nowrap">{{ $b }}</th>
                    @endforeach
                    <th class="border border-slate-200 px-2 py-2 bg-slate-200">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($checkSheet['rows'] as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="border border-slate-200 px-2 py-1.5 font-medium text-slate-700 sticky left-0 bg-white">{{ $row['kategori'] }}</td>
                        @foreach ($row['per_bulan'] as $v)
                            <td class="border border-slate-200 px-2 py-1.5 text-center {{ $v ? 'text-slate-800' : 'text-slate-300' }}">{{ $v ?: '·' }}</td>
                        @endforeach
                        <td class="border border-slate-200 px-2 py-1.5 text-center font-bold bg-slate-50">{{ $row['total'] }}</td>
                    </tr>
                @endforeach
                <tr class="bg-slate-100 font-bold">
                    <td class="border border-slate-200 px-2 py-1.5 sticky left-0 bg-slate-100">TOTAL</td>
                    @foreach ($checkSheet['total_per_bulan'] as $v)
                        <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $v }}</td>
                    @endforeach
                    <td class="border border-slate-200 px-2 py-1.5 text-center bg-slate-200">{{ $checkSheet['grand_total'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- 2. PARETO -->
    <section id="paretoCard" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
        {!! tool_header(2, 'Diagram Pareto — Jenis Ketidaksesuaian', 'Mengurutkan jenis ketidaksesuaian dari yang paling sering untuk menentukan prioritas.') !!}
        @include('partials._pareto_filter')
        <div class="relative h-72">
            @if (empty($pareto['labels']))
                <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
            @else
                <canvas id="paretoChart"></canvas>
            @endif
        </div>
    </section>
    <!-- 2b. PARETO DETAIL -->
    <section id="paretoDetailCard" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
        {!! tool_header('2b', 'Diagram Pareto — Detail Ketidaksesuaian', 'Mengurutkan detail ketidaksesuaian dari yang paling sering.') !!}
        @include('partials._pareto_detail_filter')
        <div class="relative h-72">
            @if (empty($paretoDetail['labels']))
                <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
            @else
                <canvas id="paretoDetailChart"></canvas>
            @endif
        </div>
    </section>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- 2c. PARETO CUSTOMER -->
    <section id="paretoCustCard" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
        {!! tool_header('2c', 'Diagram Pareto — Frekuensi Customer', 'Mengurutkan customer berdasarkan jumlah complaint terbanyak.') !!}
        @include('partials._pareto_cust_filter')
        <div class="relative h-72">
            @if (empty($paretoCust['labels']))
                <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
            @else
                <canvas id="paretoCustChart"></canvas>
            @endif
        </div>
    </section>
    <!-- 2d. PARETO PENYEBAB MASALAH (BARU) -->
    <section id="paretoCauseCard" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
        {!! tool_header('2d', 'Diagram Pareto — Penyebab Masalah', 'Mengurutkan jenis penyebab masalah berdasarkan frekuensi tertinggi.') !!}
        @include('partials._pareto_cause_filter')
        <div class="relative h-72">
            @if (empty($paretoCause['labels']))
                <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
            @else
                <canvas id="paretoCauseChart"></canvas>
            @endif
        </div>
    </section>
</div>

{{-- ===== HIDDEN SECTIONS (hanya disembunyikan, kode tetap ada) ===== --}}

<!-- 3. HISTOGRAM (HIDDEN) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6" style="display:none">
    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        {!! tool_header(3, 'Histogram', 'Distribusi jumlah (Qty) barang NG per complaint.') !!}
        <div class="relative h-72"><canvas id="histogramChart"></canvas></div>
    </section>
</div>

<!-- 4. CONTROL CHART (HIDDEN) -->
<section class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6" style="display:none">
    {!! tool_header(4, 'Peta Kendali / Control Chart (c-chart)', 'Memantau jumlah complaint per bulan terhadap batas kendali statistik.') !!}
    <div class="relative h-72"><canvas id="controlChart"></canvas></div>
</section>

<!-- 5. SCATTER & 7. STRATIFIKASI (HIDDEN) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6" style="display:none">
    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        {!! tool_header(5, 'Scatter Diagram', 'Hubungan antara Qty complaint dengan lead time penanganan.') !!}
        <div class="relative h-72"><canvas id="scatterChart"></canvas></div>
    </section>
    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        {!! tool_header(7, 'Stratifikasi', 'Pengelompokan complaint berdasarkan penyebab & jenis keterangan.') !!}
        <div class="relative h-80"><canvas id="stratChart"></canvas></div>
    </section>
</div>

<!-- 6. FISHBONE -->
<section class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
    {!! tool_header(6, 'Diagram Sebab-Akibat (Fishbone / Ishikawa)', 'Penyebab complaint dikelompokkan ke dalam kategori 6M.') !!}
    <div class="text-center mb-4">
        <span class="inline-block bg-rose-600 text-white text-sm font-semibold px-4 py-2 rounded-lg">
            Akibat: {{ $fishbone['efek'] }}
        </span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($fishbone['categories'] as $cat)
            <div class="border border-slate-200 rounded-lg overflow-hidden">
                <div class="bg-slate-800 text-white px-3 py-2 flex items-center justify-between">
                    <span class="font-semibold text-sm">{{ $cat['kategori'] }}</span>
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full">{{ $cat['total'] }}</span>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($cat['causes'] as $c)
                        <li class="px-3 py-2 flex items-center justify-between text-sm">
                            <span class="text-slate-700">{{ $c['nama'] }}</span>
                            <span class="text-xs font-semibold text-slate-500">{{ $c['jumlah'] }}</span>
                        </li>
                    @empty
                        <li class="px-3 py-2 text-xs text-slate-300">Tidak ada data</li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
const pareto = @json($pareto);
const paretoDetail = @json($paretoDetail);
const paretoCust = @json($paretoCust);
const paretoCause = @json($paretoCause);
const PALET = ['#0ea5e9','#6366f1','#10b981','#f59e0b','#ef4444','#ec4899','#14b8a6','#8b5cf6','#64748b','#84cc16','#f97316','#06b6d4','#a855f7','#22c55e'];

const paretoCanvas = document.getElementById('paretoChart');
if (paretoCanvas) new Chart(paretoCanvas, {
    data: { labels: pareto.labels, datasets: [
        { type:'bar', label:'Jumlah', data: pareto.values, backgroundColor:'#0ea5e9', yAxisID:'y', order:2 },
        { type:'line', label:'Kumulatif %', data: pareto.cumulative, borderColor:'#ef4444', yAxisID:'y1', tension:.3, order:1 }
    ]},
    options: { responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
        scales:{ x:{ ticks:{ font:{size:9}, maxRotation:60, minRotation:45 } },
            y:{ beginAtZero:true }, y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false} } } }
});

const paretoDetailCanvas = document.getElementById('paretoDetailChart');
if (paretoDetailCanvas) new Chart(paretoDetailCanvas, {
    data: { labels: paretoDetail.labels, datasets: [
        { type:'bar', label:'Jumlah', data: paretoDetail.values, backgroundColor:'#f59e0b', yAxisID:'y', order:2 },
        { type:'line', label:'Kumulatif %', data: paretoDetail.cumulative, borderColor:'#ef4444', yAxisID:'y1', tension:.3, order:1 }
    ]},
    options: { responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
        scales:{ x:{ ticks:{ font:{size:9}, maxRotation:60, minRotation:45 } },
            y:{ beginAtZero:true }, y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false} } } }
});

const paretoCustCanvas = document.getElementById('paretoCustChart');
if (paretoCustCanvas) new Chart(paretoCustCanvas, {
    data: { labels: paretoCust.labels, datasets: [
        { type:'bar', label:'Jumlah Complaint', data: paretoCust.values, backgroundColor:'#6366f1', yAxisID:'y', order:2 },
        { type:'line', label:'Kumulatif %', data: paretoCust.cumulative, borderColor:'#ef4444', yAxisID:'y1', tension:.3, order:1 }
    ]},
    options: { responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
        scales:{ x:{ ticks:{ font:{size:9}, maxRotation:60, minRotation:45 } },
            y:{ beginAtZero:true }, y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false} } } }
});

// Pareto Penyebab Masalah (BARU)
const paretoCauseCanvas = document.getElementById('paretoCauseChart');
if (paretoCauseCanvas) new Chart(paretoCauseCanvas, {
    data: { labels: paretoCause.labels, datasets: [
        { type:'bar', label:'Jumlah', data: paretoCause.values, backgroundColor:'#10b981', yAxisID:'y', order:2 },
        { type:'line', label:'Kumulatif %', data: paretoCause.cumulative, borderColor:'#ef4444', yAxisID:'y1', tension:.3, order:1 }
    ]},
    options: { responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
        scales:{ x:{ ticks:{ font:{size:9}, maxRotation:60, minRotation:45 } },
            y:{ beginAtZero:true }, y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false} } } }
});
</script>
@endpush
@endsection
