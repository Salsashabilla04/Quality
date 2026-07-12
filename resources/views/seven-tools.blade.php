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
    <div class="overflow-x-auto">
        <table class="w-full text-xs border-collapse">
            <thead>
                <tr class="bg-slate-100 text-slate-600">
                    <th class="border border-slate-200 px-2 py-2 text-left sticky left-0 bg-slate-100">Jenis Ketidaksesuaian</th>
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
        {!! tool_header(2, 'Diagram Pareto', 'Mengurutkan defect dari yang paling sering untuk menentukan prioritas.') !!}
        @include('partials._pareto_filter')
        <div class="relative h-72">
            @if (empty($pareto['labels']))
                <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
            @else
                <canvas id="paretoChart"></canvas>
            @endif
        </div>
    </section>
    <!-- 2b. PARETO CUSTOMER -->
    <section id="paretoCustCard" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
        {!! tool_header('2b', 'Diagram Pareto — Frekuensi Customer', 'Mengurutkan customer berdasarkan jumlah complaint terbanyak.') !!}
        @include('partials._pareto_cust_filter')
        <div class="relative h-72">
            @if (empty($paretoCust['labels']))
                <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
            @else
                <canvas id="paretoCustChart"></canvas>
            @endif
        </div>
    </section>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- 3. HISTOGRAM -->
    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        {!! tool_header(3, 'Histogram', 'Distribusi jumlah (Qty) barang NG per complaint.') !!}
        <div class="relative h-72"><canvas id="histogramChart"></canvas></div>
    </section>
</div>

<!-- 4. CONTROL CHART -->
<section class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
    {!! tool_header(4, 'Peta Kendali / Control Chart (c-chart)', 'Memantau jumlah complaint per bulan terhadap batas kendali statistik.') !!}
    <div class="flex flex-wrap gap-4 mb-3 text-xs">
        <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-700">CL (rata-rata): {{ $control['cl'] }}</span>
        <span class="px-2 py-1 rounded bg-rose-50 text-rose-700">UCL: {{ $control['ucl'] }}</span>
        <span class="px-2 py-1 rounded bg-rose-50 text-rose-700">LCL: {{ $control['lcl'] }}</span>
        <span class="px-2 py-1 rounded bg-slate-100 text-slate-600">Titik di luar kendali: {{ count($control['ooc']) }}</span>
    </div>
    <div class="relative h-72"><canvas id="controlChart"></canvas></div>
</section>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- 5. SCATTER -->
    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        {!! tool_header(5, 'Scatter Diagram', 'Hubungan antara Qty complaint dengan lead time penanganan.') !!}
        <div class="relative h-72"><canvas id="scatterChart"></canvas></div>
    </section>
    <!-- 7. STRATIFIKASI -->
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
const paretoCust = @json($paretoCust);
const histogram = @json($histogram);
const control = @json($control);
const scatter = @json($scatter);
const strat = @json($strat);
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

new Chart(document.getElementById('histogramChart'), {
    type:'bar',
    data:{ labels: histogram.labels, datasets:[{ label:'Frekuensi', data: histogram.values, backgroundColor:'#6366f1' }] },
    options:{ responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } },
        scales:{ x:{ title:{ display:true, text:'Rentang Qty (pcs)' }, ticks:{ font:{size:9} } },
            y:{ beginAtZero:true, title:{ display:true, text:'Jumlah complaint' } } } }
});

new Chart(document.getElementById('controlChart'), {
    type:'line',
    data:{ labels: control.labels, datasets:[
        { label:'Complaint', data: control.values, borderColor:'#0ea5e9', backgroundColor:'rgba(14,165,233,.1)', tension:.25, fill:true,
          pointBackgroundColor: control.values.map((v,i)=> control.ooc.includes(i)?'#ef4444':'#0ea5e9'),
          pointRadius: control.values.map((v,i)=> control.ooc.includes(i)?6:3) },
        { label:'UCL', data: control.labels.map(()=>control.ucl), borderColor:'#ef4444', borderDash:[6,4], pointRadius:0 },
        { label:'CL',  data: control.labels.map(()=>control.cl),  borderColor:'#10b981', borderDash:[4,4], pointRadius:0 },
        { label:'LCL', data: control.labels.map(()=>control.lcl), borderColor:'#ef4444', borderDash:[6,4], pointRadius:0 },
    ]},
    options:{ responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
        scales:{ y:{ beginAtZero:true } } }
});

new Chart(document.getElementById('scatterChart'), {
    type:'scatter',
    data:{ datasets:[{ label:'Complaint', data: scatter.points, backgroundColor:'#0ea5e9' }] },
    options:{ responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false },
            tooltip:{ callbacks:{ label:(c)=> c.raw.label + ' — Qty '+c.raw.x+', '+c.raw.y+' hari' } } },
        scales:{ x:{ title:{ display:true, text: scatter.x_title } },
            y:{ title:{ display:true, text:'Lead Time (hari)' } } } }
});

new Chart(document.getElementById('stratChart'), {
    type:'bar',
    data:{ labels: strat.penyebab.labels, datasets:[{ label:'Jumlah', data: strat.penyebab.values,
        backgroundColor: strat.penyebab.labels.map((_,i)=>PALET[i%PALET.length]) }] },
    options:{ indexAxis:'y', responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } },
        scales:{ x:{ beginAtZero:true }, y:{ ticks:{ font:{size:10} } } } }
});
</script>
@endpush
@endsection
