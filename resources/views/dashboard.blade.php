@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan monitoring Customer Complaint & Non-Conformance Report')

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
    $role = auth()->user()->role;
    $isQa = $role === 'qa';
    $isSpv = $role === 'supervisor';

    $overdueQa = \App\Models\Complaint::whereIn('status', ['Open', 'Diproses'])->where('tanggal_complain', '<=', now()->subDays(3))->count();
    $pendingSpvTotal = \App\Models\Complaint::where('supervisor_approval', 'Pending')->count();
    $revisiQaTotal = \App\Models\Complaint::where('supervisor_approval', 'Rejected')->count();
    $approvedSpvTotal = \App\Models\Complaint::where('supervisor_approval', 'Approved')->count();
    $visitActiveTotal = \App\Models\Complaint::where('perlu_visit', true)->count();

    $cards = [
        ['Total Complaint', $kpi['total_complaint'], 'kasus (all time)', 'bg-sky-500', route('complaints.index')],
        ['NCR Divalidasi ✅', $approvedSpvTotal, 'divalidasi SPV', 'bg-emerald-500', route('complaints.index', ['approval' => 'Approved'])],
        ['NCR Belum Validasi ⏳', $pendingSpvTotal, 'menunggu review SPV', 'bg-purple-500', route('complaints.index', ['approval' => 'Pending'])],
        ['NCR Butuh Revisi 🔄', $revisiQaTotal, 'dikembalikan ke QA', 'bg-rose-500', route('complaints.index', ['revisi' => 1])],
        ['Status Open', $kpi['open'], 'belum selesai', 'bg-amber-500', route('complaints.index', ['status' => 'Open'])],
        ['Status Close', $kpi['close'].' / '.$kpi['total_complaint'], $kpi['persen_close'].'% selesai', 'bg-teal-500', route('complaints.index', ['status' => 'Close'])],
        ['Jumlah Customer', $kpi['jumlah_customer'], 'pihak/klien', 'bg-indigo-500', route('customers.index')],
        ['Total Qty NG', number_format($kpi['total_qty']), 'pcs terdeteksi', 'bg-orange-500', null],
    ];
@endphp

{{-- EWS Alert Banners --}}
@if(($isQa || !$isSpv) && $revisiQaTotal > 0)
    <div class="mb-5 bg-rose-50 border-2 border-rose-300 rounded-2xl p-5 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-rose-600 text-white flex items-center justify-center font-extrabold text-xl shrink-0">!</div>
            <div>
                <div class="text-sm font-bold text-rose-900">⚠️ PERHATIAN QA: {{ $revisiQaTotal }} Dokumen NCR Dikembalikan untuk Perbaikan!</div>
                <div class="text-xs text-rose-700 mt-0.5">Supervisor QC telah memberikan instruksi perbaikan pada investigasi 6M/CAPA.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['revisi' => 1]) }}" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm whitespace-nowrap">Perbaiki Kasus Sekarang →</a>
    </div>
@endif

@if(($isQa || !$isSpv) && $pendingSpvTotal > 0)
    <div class="mb-5 bg-purple-50 border-2 border-purple-300 rounded-2xl p-5 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-extrabold text-xl shrink-0">⏳</div>
            <div>
                <div class="text-sm font-bold text-purple-900">🛡️ MENUNGGU VALIDASI: {{ $pendingSpvTotal }} Dokumen NCR Menunggu Persetujuan Supervisor!</div>
                <div class="text-xs text-purple-700 mt-0.5">Dokumen telah diajukan dan sedang dalam antrean peninjauan oleh Supervisor QC.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm whitespace-nowrap">Lihat Kasus Pending →</a>
    </div>
@endif

@if(($isQa || !$isSpv) && $approvedSpvTotal > 0)
    <div class="mb-5 bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-5 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-xl shrink-0">📄</div>
            <div>
                <div class="text-sm font-bold text-emerald-900">✅ SURAT NCR DIVALIDASI SPV: {{ $approvedSpvTotal }} Dokumen Surat NCR Telah Disetujui!</div>
                <div class="text-xs text-emerald-700 mt-0.5">Supervisor QC telah memvalidasi dokumen. Tim QA dapat langsung mengunduh & mencetak Surat NCR PDF resmi.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['approval' => 'Approved']) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm whitespace-nowrap">Lihat & Unduh Surat NCR →</a>
    </div>
@endif

@if(($isQa || !$isSpv) && ($overdueQa > 0 || $kpi['open'] > 0))
    <div class="mb-5 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl flex items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-2 text-xs">
            <span class="text-sm">⚠️</span>
            <span>Terdapat <span class="font-bold">{{ $kpi['open'] > 0 ? $kpi['open'] : $overdueQa }} kasus komplain</span> yang perlu untuk ditinjau dan ditindaklanjuti.</span>
        </div>
        <a href="{{ route('complaints.index', ['status' => 'Open']) }}" class="text-xs font-bold text-amber-900 underline hover:text-amber-950 shrink-0">Tinjau Kasus Sekarang →</a>
    </div>
@endif

@if($isSpv && $pendingSpvTotal > 0)
    <div class="mb-6 bg-purple-50 border-2 border-purple-300 rounded-2xl p-5 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-extrabold text-xl shrink-0">⏳</div>
            <div>
                <div class="text-sm font-bold text-purple-900">🛡️ APPROVAL SPV: {{ $pendingSpvTotal }} Dokumen NCR Menunggu Persetujuan Anda!</div>
                <div class="text-xs text-purple-700 mt-0.5">Silakan tinjau kelayakan data sebelum memberikan approval atau menentukan jadwal kunjungan.</div>
            </div>
        </div>
        <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm whitespace-nowrap">Tinjau Kasus Pending →</a>
    </div>
@endif

@if($isSpv && $visitActiveTotal > 0)
    <div class="mb-6 bg-indigo-50 border-2 border-indigo-300 rounded-2xl p-4 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xl">🚐</span>
            <div class="text-xs text-indigo-900 font-semibold">JADWAL VISIT: Terdapat {{ $visitActiveTotal }} kunjungan lapangan ke customer yang sedang aktif.</div>
        </div>
        <a href="{{ route('visit.index') }}" class="text-xs font-bold text-indigo-800 underline">Buka Kalender Visit →</a>
    </div>
@endif

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach ($cards as [$label, $value, $sub, $color, $url])
        @if($url)
            <a href="{{ $url }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md hover:border-slate-300 transition-all group block">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $color }} shrink-0"></span>
                        <span class="text-xs text-slate-600 font-semibold group-hover:text-sky-600 transition-colors">{{ $label }}</span>
                    </div>
                    <span class="text-xs text-slate-300 group-hover:text-sky-500 transition-colors">→</span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-900 truncate">{{ $value }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{!! $sub !!}</div>
            </a>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $color }} shrink-0"></span>
                    <span class="text-xs text-slate-500 font-medium">{{ $label }}</span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-900 truncate">{{ $value }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{!! $sub !!}</div>
            </div>
        @endif
    @endforeach
</div>

<div class="grid grid-cols-1 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="font-semibold text-slate-800 mb-1">Trend Complaint</h2>
                <p class="text-xs text-slate-500">Menampilkan perkembangan jumlah customer complaint sebagai indikator performa kualitas produk.</p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                @foreach (request()->except('trend_type') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <select name="trend_type" onchange="this.form.submit()" class="text-xs font-medium rounded-lg border-slate-200 focus:ring-sky-500 focus:border-sky-500 bg-slate-50">
                    <option value="bulan" {{ $trendType === 'bulan' ? 'selected' : '' }}>Trend Bulanan</option>
                    <option value="tahun" {{ $trendType === 'tahun' ? 'selected' : '' }}>Trend Tahunan</option>
                </select>
            </form>
        </div>
        <div class="relative h-72"><canvas id="trendChart"></canvas></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div id="paretoDetailCard" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
        <h2 class="font-semibold text-slate-800 mb-1">Diagram Pareto — Detail Ketidaksesuaian</h2>
        <p class="text-xs text-slate-500 mb-4">Detail uraian ketidaksesuaian dengan frekuensi tertinggi (aturan 80/20).</p>
        @include('partials._pareto_detail_filter')
        <div class="relative h-72">
            @if (empty($paretoDetail['labels']))
                <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
            @else
                <canvas id="paretoDetailChart"></canvas>
            @endif
        </div>
    </div>

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
            <div>
                <h2 class="font-semibold text-slate-800">📊 Grafik Pola Keterkaitan Cacat (Lift Ratio — Apriori)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Tingkat kekuatan kaitan sebab-akibat antara jenis ketidaksesuaian dan penyebab utama.</p>
            </div>
            <a href="{{ route('apriori') }}" class="text-xs text-sky-600 font-medium hover:underline whitespace-nowrap ml-2">Lihat semua →</a>
        </div>
        
        @if(empty($topRules))
            <p class="text-sm text-slate-400 py-8 text-center">Belum ada pola. Tambah data complaint untuk melihat hasil.</p>
        @else
            <div class="relative h-64 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                <canvas id="qaAprioriChart"></canvas>
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
