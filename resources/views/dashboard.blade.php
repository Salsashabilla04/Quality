@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan monitoring Customer Complaint & Non-Conformance Report')

@section('actions')
    <div class="flex items-center gap-2">
        {{-- Laporan PDF --}}
        <a href="{{ route('export.laporan.pdf') }}" class="btn btn-danger btn-sm whitespace-nowrap">
            <i class="fas fa-file-pdf"></i>
            <span>Laporan PDF</span>
        </a>
    </div>
@endsection

@section('content')
@php
    $role = auth()->user()->role;
    $isQa = $role === 'qa';
    $isSpv = $role === 'supervisor';

    $overdueQa = \App\Models\Complaint::whereIn('status', ['Open', 'Diproses'])->where('tanggal_complain', '<=', now()->subDays(3))->count();
    $pendingSpvTotal = \App\Models\Complaint::where('supervisor_approval', 'Pending')->count();
    $revisiQaTotal = \App\Models\Complaint::where('supervisor_approval', 'Rejected')->count();
    $approvedSpvTotal = \App\Models\Complaint::where('supervisor_approval', 'Approved')->count();
    $visitActiveTotal = \App\Models\Complaint::where('perlu_visit', true)->count();

    $cards = [
        ['Total Complaint', $kpi['total_complaint'], 'kasus (all time)', 'border-left-primary', 'text-[#4e73df]', 'fa-clipboard-list', route('complaints.index')],
        ['NCR Divalidasi', $approvedSpvTotal, 'divalidasi SPV', 'border-left-success', 'text-[#1cc88a]', 'fa-check-circle', route('complaints.index', ['approval' => 'Approved'])],
        ['NCR Belum Validasi', $pendingSpvTotal, 'menunggu review SPV', 'border-left-purple', 'text-[#6f42c1]', 'fa-clock', route('complaints.index', ['approval' => 'Pending'])],
        ['NCR Butuh Revisi', $revisiQaTotal, 'dikembalikan ke QA', 'border-left-danger', 'text-[#e74a3b]', 'fa-exclamation-triangle', route('complaints.index', ['revisi' => 1])],
        ['Status Open', $kpi['open'], 'belum selesai', 'border-left-warning', 'text-[#f6c23e]', 'fa-folder-open', route('complaints.index', ['status' => 'Open'])],
        ['Status Close', $kpi['close'].' / '.$kpi['total_complaint'], $kpi['persen_close'].'% selesai', 'border-left-info', 'text-[#36b9cc]', 'fa-tasks', route('complaints.index', ['status' => 'Close'])],
        ['Jumlah Customer', $kpi['jumlah_customer'], 'pihak/klien', 'border-left-primary', 'text-[#4e73df]', 'fa-users', route('customers.index')],
        ['Total Qty NG', number_format($kpi['total_qty']), 'pcs terdeteksi', 'border-left-orange', 'text-[#fd7e14]', 'fa-boxes', null],
    ];
@endphp

{{-- EWS Alert Banners --}}
@if(($isQa || !$isSpv) && $revisiQaTotal > 0)
    <div class="mb-4 bg-white border-l-4 border-l-[#e74a3b] rounded shadow-sm p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-rose-100 text-[#e74a3b] flex items-center justify-center font-bold text-lg shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-gray-800">PERHATIAN QA: {{ $revisiQaTotal }} Dokumen NCR Dikembalikan untuk Perbaikan!</div>
                <div class="text-xs text-gray-500 mt-0.5">Supervisor QC telah memberikan instruksi perbaikan pada investigasi 6M/CAPA.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['revisi' => 1]) }}" class="btn btn-danger btn-sm whitespace-nowrap">
            <span>Perbaiki Kasus Sekarang</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
@endif

@if(($isQa || !$isSpv) && $pendingSpvTotal > 0)
    <div class="mb-4 bg-white border-l-4 border-l-[#6f42c1] rounded shadow-sm p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-purple-100 text-[#6f42c1] flex items-center justify-center font-bold text-lg shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-gray-800">Menunggu Validasi: {{ $pendingSpvTotal }} Dokumen NCR Menunggu Persetujuan Supervisor!</div>
                <div class="text-xs text-gray-500 mt-0.5">Dokumen telah diajukan dan sedang dalam antrean peninjauan oleh Supervisor QC.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}" class="btn btn-info btn-sm whitespace-nowrap">
            <span>Lihat Kasus Pending</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
@endif


@if($isSpv && $pendingSpvTotal > 0)
    <div class="mb-4 bg-white border-l-4 border-l-[#6f42c1] rounded shadow-sm p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-purple-100 text-[#6f42c1] flex items-center justify-center font-bold text-lg shrink-0">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-gray-800">APPROVAL SPV: {{ $pendingSpvTotal }} Dokumen NCR Menunggu Persetujuan Anda!</div>
                <div class="text-xs text-gray-500 mt-0.5">Silakan tinjau kelayakan data sebelum memberikan approval atau menentukan jadwal kunjungan.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}" class="bg-[#6f42c1] hover:bg-purple-800 text-white text-xs font-bold px-3.5 py-2 rounded shadow-sm whitespace-nowrap">Tinjau Kasus Pending &rarr;</a>
    </div>
@endif

@if($isSpv && $visitActiveTotal > 0)
    <div class="mb-4 bg-white border-l-4 border-l-[#36b9cc] rounded shadow-sm p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-truck text-[#36b9cc] text-xl"></i>
            <div class="text-xs text-gray-700 font-semibold">JADWAL VISIT: Terdapat {{ $visitActiveTotal }} kunjungan lapangan ke customer yang sedang aktif.</div>
        </div>
        <a href="{{ route('visit.index') }}" class="text-xs font-bold text-[#36b9cc] hover:underline">Buka Kalender Visit &rarr;</a>
    </div>
@endif

{{-- SB Admin 2 Metric KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach ($cards as [$label, $value, $sub, $borderClass, $textClass, $icon, $url])
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

{{-- Trend Complaint Card (SB Admin 2 Card Format) --}}
<div class="bg-white rounded shadow-sm border border-gray-200 mb-6 overflow-hidden">
    <div class="bg-[#f8f9fc] border-b border-gray-200 py-3 px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-bold text-[#4e73df] text-base mb-0.5">Trend Complaint</h2>
            <p class="text-xs text-gray-500">Menampilkan perkembangan jumlah customer complaint sebagai indikator performa kualitas produk.</p>
        </div>
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            @foreach (request()->except('trend_type') as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <select name="trend_type" onchange="this.form.submit()" class="text-xs font-semibold rounded border-gray-300 focus:ring-[#4e73df] focus:border-[#4e73df] bg-white text-gray-700 py-1.5 px-3">
                <option value="bulan" {{ $trendType === 'bulan' ? 'selected' : '' }}>Trend Bulanan</option>
                <option value="tahun" {{ $trendType === 'tahun' ? 'selected' : '' }}>Trend Tahunan</option>
            </select>
        </form>
    </div>
    <div class="p-4 md:p-6">
        <div class="relative h-72"><canvas id="trendChart"></canvas></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div id="paretoDetailCard" class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden scroll-mt-6">
        <div class="bg-[#f8f9fc] border-b border-gray-200 py-3 px-4">
            <h2 class="font-bold text-[#4e73df] text-base mb-0.5">Diagram Pareto — Detail Ketidaksesuaian</h2>
            <p class="text-xs text-gray-500">Detail uraian ketidaksesuaian dengan frekuensi tertinggi (aturan 80/20).</p>
        </div>
        <div class="p-4 md:p-6">
            @include('partials._pareto_detail_filter')
            <div class="relative h-72">
                @if (empty($paretoDetail['labels']))
                    <div class="absolute inset-0 flex items-center justify-center text-sm text-gray-400">Tidak ada data pada periode ini.</div>
                @else
                    <canvas id="paretoDetailChart"></canvas>
                @endif
            </div>
        </div>
    </div>

    <div id="paretoCustCard" class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden scroll-mt-6">
        <div class="bg-[#f8f9fc] border-b border-gray-200 py-3 px-4">
            <h2 class="font-bold text-[#4e73df] text-base mb-0.5">Diagram Pareto — Frekuensi Customer</h2>
            <p class="text-xs text-gray-500">Customer dengan complaint terbanyak (aturan 80/20).</p>
        </div>
        <div class="p-4 md:p-6">
            @include('partials._pareto_cust_filter')
            <div class="relative h-72">
                @if (empty($paretoCust['labels']))
                    <div class="absolute inset-0 flex items-center justify-center text-sm text-gray-400">Tidak ada data pada periode ini.</div>
                @else
                    <canvas id="paretoCustChart"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-[#f8f9fc] border-b border-gray-200 py-3 px-4 flex items-center justify-between">
            <h2 class="font-bold text-[#4e73df] text-base">Stratifikasi per Area</h2>
            <span class="text-[11px] text-gray-400">Top {{ count($strat['area']['labels']) }} area</span>
        </div>
        <div class="p-4 md:p-6">
            <p class="text-xs text-gray-500 mb-4">Sebaran complaint berdasarkan area produksi (terbanyak di atas).</p>
            <div class="relative" style="height: {{ max(260, count($strat['area']['labels']) * 34) }}px">
                <canvas id="areaChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-2 bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-[#f8f9fc] border-b border-gray-200 py-3 px-4 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-[#4e73df] text-base flex items-center gap-2">
                    <i class="fas fa-chart-bar text-sm"></i>
                    <span>Grafik Pola Keterkaitan Cacat (Lift Ratio — Apriori)</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Tingkat kekuatan kaitan sebab-akibat antara jenis ketidaksesuaian dan penyebab utama.</p>
            </div>
            <a href="{{ route('apriori') }}" class="text-xs text-[#4e73df] font-bold hover:underline whitespace-nowrap ml-2">Lihat semua &rarr;</a>
        </div>
        <div class="p-4 md:p-6">
            @if(empty($topRules))
                <p class="text-sm text-gray-400 py-8 text-center">Belum ada pola. Tambah data complaint untuk melihat hasil.</p>
            @else
                <div class="relative h-64 bg-[#f8f9fc] p-3 rounded border border-gray-200">
                    <canvas id="qaAprioriChart"></canvas>
                </div>
                <div class="flex flex-wrap items-center justify-between text-[11px] text-gray-500 mt-3 gap-2">
                    <span>Berdasarkan <b>{{ $totalTrx }}</b> transaksi komplain &middot; Terurut dari kaitan terkuat</span>
                    <div class="flex items-center gap-3 font-semibold">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-[#e74a3b] inline-block"></span> Sangat Kuat (&ge;5x)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-[#f6c23e] inline-block"></span> Kuat (&ge;3x)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-[#4e73df] inline-block"></span> Sedang (&ge;1.5x)</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
const pareto = @json($pareto);
const paretoDetail = @json($paretoDetail);
const paretoCust = @json($paretoCust);
const trend = @json($trend);
const strat = @json($strat);

const paretoCanvas = document.getElementById('paretoChart');
if (paretoCanvas) new Chart(paretoCanvas, {
    data: {
        labels: pareto.labels,
        datasets: [
            { type: 'bar', label: 'Jumlah', data: pareto.values, backgroundColor: '#0ea5e9', yAxisID: 'y', order: 2, borderRadius: 4 },
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

const paretoDetailCanvas = document.getElementById('paretoDetailChart');
if (paretoDetailCanvas && paretoDetail.labels && paretoDetail.labels.length) {
    new Chart(paretoDetailCanvas, {
        type: 'bar',
        data: {
            labels: paretoDetail.labels,
            datasets: [
                { type: 'bar', label: 'Jumlah', data: paretoDetail.values, backgroundColor: '#f59e0b', yAxisID: 'y', order: 2, borderRadius: 4 },
                { type: 'line', label: 'Kumulatif %', data: paretoDetail.cumulative, borderColor: '#ef4444',
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
}

const paretoCustCanvas = document.getElementById('paretoCustChart');
if (paretoCustCanvas) new Chart(paretoCustCanvas, {
    type: 'bar',
    data: {
        labels: paretoCust.labels,
        datasets: [
            { type: 'bar', label: 'Jumlah Complaint', data: paretoCust.values, backgroundColor: '#6366f1', yAxisID: 'y', order: 2, borderRadius: 4 },
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

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trend.labels,
        datasets: [
            { label: 'Jumlah Complaint', data: trend.values, borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,.1)',
              tension: 0.25, fill: true, pointBackgroundColor: '#0ea5e9', pointRadius: 4 }
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

// ── Apriori Chart QA ──────────────────────────────────────────────────────────
const qaTopRules = @json($topRules);
const qaAprioriCanvas = document.getElementById('qaAprioriChart');
if (qaAprioriCanvas && qaTopRules.length > 0) {
    const labels = qaTopRules.map(r => r.antecedents + ' ➔ ' + r.consequents);
    const dataLift = qaTopRules.map(r => r.lift);
    const bgColors = qaTopRules.map(r => {
        if (r.lift >= 5) return '#ef4444';
        if (r.lift >= 3) return '#f59e0b';
        if (r.lift >= 1.5) return '#0ea5e9';
        return '#94a3b8';
    });

    new Chart(qaAprioriCanvas, {
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
                            const r = qaTopRules[ctx.dataIndex];
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
</script>
@endpush
@endsection
