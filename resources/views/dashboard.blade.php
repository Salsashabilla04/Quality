@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan monitoring Customer Complaint & Non-Conformance Report')

@section('actions')
    <a href="{{ route('export.laporan.pdf') }}"
       class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        Laporan PDF
    </a>
@endsection

@section('content')
@php
    $cards = [
        ['Total Complaint', $kpi['total_complaint'], 'kasus', 'bg-sky-500'],
        ['Status Close', $kpi['close'].' / '.$kpi['total_complaint'], $kpi['persen_close'].'% selesai', 'bg-emerald-500'],
        ['Status Open', $kpi['open'], 'belum selesai', 'bg-amber-500'],
        ['Total Qty NG', number_format($kpi['total_qty']), 'pcs', 'bg-rose-500'],
        ['Defect Tertinggi', $kpi['top_defect'] ?? '-', ($kpi['top_defect_n'] ?? 0).' kasus', 'bg-indigo-500'],
        ['Penyebab Utama', $kpi['top_cause'] ?? '-', ($kpi['top_cause_n'] ?? 0).' kasus', 'bg-fuchsia-500'],
        ['Rata-rata Lead Time', $kpi['avg_lead_time'], 'hari', 'bg-teal-500'],
        ['Jumlah Customer', $kpi['jumlah_customer'], 'customer', 'bg-slate-500'],
    ];
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach ($cards as [$label, $value, $sub, $color])
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full {{ $color }}"></span>
                <span class="text-xs text-slate-500 font-medium">{{ $label }}</span>
            </div>
            <div class="mt-2 text-2xl font-bold text-slate-900 truncate">{{ $value }}</div>
            <div class="text-xs text-slate-400">{{ $sub }}</div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div id="paretoCard" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
        <h2 class="font-semibold text-slate-800 mb-1">Diagram Pareto — Jenis Ketidaksesuaian</h2>
        <p class="text-xs text-slate-500 mb-4">Prioritas perbaikan: fokus pada defect dengan frekuensi tertinggi (aturan 80/20).</p>
        @include('partials._pareto_filter')
        <div class="relative h-72">
            @if (empty($pareto['labels']))
                <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
            @else
                <canvas id="paretoChart"></canvas>
            @endif
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <h2 class="font-semibold text-slate-800 mb-1">Peta Kendali (c-chart) — Complaint per Bulan</h2>
        <p class="text-xs text-slate-500 mb-4">Memantau kestabilan jumlah complaint terhadap batas kendali (UCL/LCL).</p>
        <div class="relative h-72"><canvas id="controlChart"></canvas></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div id="paretoCustCard" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
        <h2 class="font-semibold text-slate-800 mb-1">Diagram Pareto — Frekuensi Customer</h2>
        <p class="text-xs text-slate-500 mb-4">Customer dengan complaint terbanyak (aturan 80/20).</p>
        @include('partials._pareto_cust_filter')
        <div class="relative h-72">
            @if (empty($paretoCust['labels']))
                <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
            @else
                <canvas id="paretoCustChart"></canvas>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-1">
            <h2 class="font-semibold text-slate-800">Stratifikasi per Area</h2>
            <span class="text-[11px] text-slate-400">Top {{ count($strat['area']['labels']) }} area</span>
        </div>
        <p class="text-xs text-slate-500 mb-4">Sebaran complaint berdasarkan area produksi (terbanyak di atas).</p>
        <div class="relative" style="height: {{ max(260, count($strat['area']['labels']) * 34) }}px">
            <canvas id="areaChart"></canvas>
        </div>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-slate-800">Top 5 Association Rules (Apriori)</h2>
            <a href="{{ route('apriori') }}" class="text-xs text-sky-600 hover:underline">Lihat semua →</a>
        </div>
        <div class="space-y-3">
            @forelse ($topRules as $r)
                <div class="border border-slate-100 rounded-lg p-3 bg-slate-50">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-semibold text-slate-700">{{ $r['antecedents'] }}</span>
                        <span class="text-slate-400">→</span>
                        <span class="font-semibold text-sky-700">{{ $r['consequents'] }}</span>
                        <span class="ml-auto text-[11px] px-2 py-0.5 rounded-full
                            {{ $r['kekuatan'] === 'Sangat Kuat' ? 'bg-rose-100 text-rose-700' : ($r['kekuatan'] === 'Kuat' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600') }}">
                            {{ $r['kekuatan'] }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $r['interpretasi'] }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada rule yang memenuhi ambang. Tambah data complaint untuk melihat hasil.</p>
            @endforelse
        </div>
        <p class="text-[11px] text-slate-400 mt-3">Berdasarkan {{ $totalTrx }} transaksi · min. support 5% · min. confidence 50%</p>
    </div>
</div>

@push('scripts')
<script>
const pareto = @json($pareto);
const paretoCust = @json($paretoCust);
const control = @json($control);
const strat = @json($strat);

const paretoCanvas = document.getElementById('paretoChart');
if (paretoCanvas) new Chart(paretoCanvas, {
    data: {
        labels: pareto.labels,
        datasets: [
            { type: 'bar', label: 'Jumlah', data: pareto.values, backgroundColor: '#0ea5e9', yAxisID: 'y', order: 2 },
            { type: 'line', label: 'Kumulatif %', data: pareto.cumulative, borderColor: '#ef4444',
              backgroundColor: '#ef4444', yAxisID: 'y1', tension: 0.3, order: 1, pointRadius: 3 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { boxWidth: 12, font: { size: 11 } } } },
        scales: {
            x: { ticks: { font: { size: 9 }, maxRotation: 60, minRotation: 45 } },
            y: { beginAtZero: true, title: { display: true, text: 'Frekuensi' } },
            y1: { beginAtZero: true, max: 100, position: 'right', grid: { drawOnChartArea: false },
                  title: { display: true, text: '%' } }
        }
    }
});

const paretoCustCanvas = document.getElementById('paretoCustChart');
if (paretoCustCanvas) new Chart(paretoCustCanvas, {
    data: {
        labels: paretoCust.labels,
        datasets: [
            { type: 'bar', label: 'Jumlah Complaint', data: paretoCust.values, backgroundColor: '#6366f1', yAxisID: 'y', order: 2 },
            { type: 'line', label: 'Kumulatif %', data: paretoCust.cumulative, borderColor: '#ef4444',
              backgroundColor: '#ef4444', yAxisID: 'y1', tension: 0.3, order: 1, pointRadius: 3 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { boxWidth: 12, font: { size: 11 } } } },
        scales: {
            x: { ticks: { font: { size: 9 }, maxRotation: 60, minRotation: 45 } },
            y: { beginAtZero: true, title: { display: true, text: 'Frekuensi' } },
            y1: { beginAtZero: true, max: 100, position: 'right', grid: { drawOnChartArea: false },
                  title: { display: true, text: '%' } }
        }
    }
});

new Chart(document.getElementById('controlChart'), {
    type: 'line',
    data: {
        labels: control.labels,
        datasets: [
            { label: 'Complaint', data: control.values, borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,.1)',
              tension: 0.25, fill: true,
              pointBackgroundColor: control.values.map((v,i)=> control.ooc.includes(i) ? '#ef4444' : '#0ea5e9'),
              pointRadius: control.values.map((v,i)=> control.ooc.includes(i) ? 6 : 3) },
            { label: 'UCL', data: control.labels.map(()=>control.ucl), borderColor: '#ef4444', borderDash:[6,4], pointRadius:0 },
            { label: 'CL',  data: control.labels.map(()=>control.cl),  borderColor: '#10b981', borderDash:[4,4], pointRadius:0 },
            { label: 'LCL', data: control.labels.map(()=>control.lcl), borderColor: '#ef4444', borderDash:[6,4], pointRadius:0 },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { boxWidth: 12, font: { size: 11 } } } },
        scales: { x: { ticks: { font: { size: 10 } } }, y: { beginAtZero: true } }
    }
});

const areaTotal = strat.area.values.reduce((a, b) => a + b, 0) || 1;
new Chart(document.getElementById('areaChart'), {
    type: 'bar',
    data: {
        labels: strat.area.labels,
        datasets: [{
            label: 'Jumlah complaint',
            data: strat.area.values,
            backgroundColor: '#0ea5e9',
            hoverBackgroundColor: '#0284c7',
            borderRadius: 6,
            barThickness: 20,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: { right: 36 } },
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: (c) => ` ${c.raw} complaint (${(c.raw / areaTotal * 100).toFixed(1)}%)` } },
            datalabels: {
                display: true,
                anchor: 'end',
                align: 'end',
                color: '#475569',
                font: { size: 11, weight: '600' },
                formatter: (v) => `${v} (${(v / areaTotal * 100).toFixed(0)}%)`,
            }
        },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0, font: { size: 10 } } },
            y: { grid: { display: false }, ticks: { font: { size: 11, weight: '500' } } }
        }
    }
});
</script>
@endpush
@endsection
