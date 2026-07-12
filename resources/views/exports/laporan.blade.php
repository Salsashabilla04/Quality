<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { font-size: 11px; color: #1e293b; }
    h1 { font-size: 16px; margin: 0; }
    h2 { font-size: 13px; margin: 18px 0 6px; border-bottom: 2px solid #0ea5e9; padding-bottom: 3px; color: #0c4a6e; }
    .sub { color: #64748b; font-size: 10px; margin-top: 2px; }
    table { width: 100%; border-collapse: collapse; margin-top: 4px; }
    th, td { border: 1px solid #cbd5e1; padding: 4px 6px; text-align: left; }
    th { background: #0ea5e9; color: #fff; font-size: 10px; }
    td { font-size: 10px; }
    .kpi { width: 100%; margin-top: 6px; }
    .kpi td { border: 1px solid #e2e8f0; }
    .kpi .label { color: #64748b; font-size: 9px; }
    .kpi .val { font-size: 14px; font-weight: bold; }
    .badge { padding: 1px 5px; border-radius: 6px; font-size: 9px; }
    .muted { color: #64748b; }
    .right { text-align: right; }
    .center { text-align: center; }
    .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
    <div style="border-bottom:3px solid #0c4a6e; padding-bottom:6px;">
        <h1>Laporan Analisis Customer Complaint &amp; NCR</h1>
        <div class="sub">PT Wahana Bermuda Nusantara &middot; Seven Tools QC &amp; Algoritma Apriori &middot; {{ $tanggal }}</div>
    </div>

    <h2>A. Ringkasan (KPI)</h2>
    <table class="kpi">
        <tr>
            <td><div class="label">Total Complaint</div><div class="val">{{ $kpi['total_complaint'] }}</div></td>
            <td><div class="label">Close / Open</div><div class="val">{{ $kpi['close'] }} / {{ $kpi['open'] }}</div></td>
            <td><div class="label">Total Qty NG</div><div class="val">{{ number_format($kpi['total_qty']) }}</div></td>
            <td><div class="label">Rata-rata Lead Time</div><div class="val">{{ $kpi['avg_lead_time'] }} hari</div></td>
        </tr>
        <tr>
            <td><div class="label">Defect Tertinggi</div><div class="val" style="font-size:11px">{{ $kpi['top_defect'] }} ({{ $kpi['top_defect_n'] }})</div></td>
            <td><div class="label">Penyebab Utama</div><div class="val" style="font-size:11px">{{ $kpi['top_cause'] }} ({{ $kpi['top_cause_n'] }})</div></td>
            <td><div class="label">Jumlah Customer</div><div class="val">{{ $kpi['jumlah_customer'] }}</div></td>
            <td><div class="label">% Close</div><div class="val">{{ $kpi['persen_close'] }}%</div></td>
        </tr>
    </table>

    <h2>B. Diagram Pareto — Jenis Ketidaksesuaian</h2>
    <table>
        <tr><th>No</th><th>Jenis Ketidaksesuaian</th><th class="right">Frekuensi</th><th class="right">% Kumulatif</th></tr>
        @foreach ($pareto['labels'] as $i => $label)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $label }}</td>
                <td class="right">{{ $pareto['values'][$i] }}</td>
                <td class="right">{{ $pareto['cumulative'][$i] }}%</td>
            </tr>
        @endforeach
        <tr><td colspan="2"><b>Total</b></td><td class="right"><b>{{ $pareto['total'] }}</b></td><td></td></tr>
    </table>

    <h2>C. Diagram Sebab-Akibat (Fishbone 6M) — Penyebab</h2>
    <table>
        <tr><th>Kategori 6M</th><th>Penyebab</th><th class="right">Jumlah</th></tr>
        @foreach ($fishbone['categories'] as $cat)
            @foreach ($cat['causes'] as $idx => $c)
                <tr>
                    @if ($idx === 0)<td rowspan="{{ count($cat['causes']) }}"><b>{{ $cat['kategori'] }}</b></td>@endif
                    <td>{{ $c['nama'] }}</td>
                    <td class="right">{{ $c['jumlah'] }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>

    <h2>D. Hasil Algoritma Apriori</h2>
    <div class="sub muted">Total transaksi: {{ $totalTrx }} &middot; min. support 5% &middot; min. confidence 50%</div>

    <h3 style="font-size:11px; margin:10px 0 2px;">D.1 Frequent Itemsets</h3>
    <table>
        <tr><th>Itemset</th><th class="center">Size</th><th class="right">Support</th><th class="right">Jumlah</th></tr>
        @foreach ($frequent as $f)
            <tr>
                <td>{{ implode(', ', array_map(fn($i)=>str_replace(['Ketidaksesuaian=','Penyebab='],'',$i), $f['items'])) }}</td>
                <td class="center">{{ $f['size'] }}</td>
                <td class="right">{{ number_format($f['support']*100,1) }}%</td>
                <td class="right">{{ $f['count'] }}</td>
            </tr>
        @endforeach
    </table>

    <h3 style="font-size:11px; margin:10px 0 2px;">D.2 Association Rules</h3>
    <table>
        <tr><th>Jika</th><th>Maka</th><th class="right">Supp.</th><th class="right">Conf.</th><th class="right">Lift</th><th class="center">Kekuatan</th></tr>
        @forelse ($rules as $r)
            <tr>
                <td>{{ $r['antecedents'] }}</td>
                <td>{{ $r['consequents'] }}</td>
                <td class="right">{{ number_format($r['support']*100,1) }}%</td>
                <td class="right">{{ number_format($r['confidence']*100,1) }}%</td>
                <td class="right">{{ number_format($r['lift'],2) }}</td>
                <td class="center">{{ $r['kekuatan'] }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="center muted">Tidak ada rule.</td></tr>
        @endforelse
    </table>

    @if (count($rules))
        <h3 style="font-size:11px; margin:10px 0 2px;">D.3 Interpretasi</h3>
        @foreach ($rules as $r)
            <div style="margin:2px 0; font-size:10px;">&bull; {{ $r['interpretasi'] }}</div>
        @endforeach
    @endif

    <div class="footer">
        Dibuat otomatis oleh Sistem Monitoring Complaint &amp; NCR &middot; {{ $tanggal }}
    </div>
</body>
</html>
