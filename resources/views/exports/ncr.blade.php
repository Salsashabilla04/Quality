<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { font-size: 11px; color: #111; margin: 0; }
    .wrap { padding: 4px 6px; }
    table { border-collapse: collapse; }
    .hdr { width: 100%; }
    .logo { font-size: 26px; font-weight: bold; letter-spacing: -1px; }
    .docbox { border: 1px solid #000; font-size: 10px; }
    .docbox td { padding: 2px 8px; }
    .title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 13px; }
    .subtitle { text-align: center; font-size: 11px; margin-bottom: 6px; }
    .infotbl { width: 100%; border: 1px solid #000; margin-bottom: 8px; }
    .infotbl td { border: 1px solid #000; padding: 4px 8px; }
    .infotbl .lbl { width: 28%; }
    .section { border: 1px solid #000; padding: 6px 8px; margin-bottom: 6px; }
    .section h4 { margin: 0 0 4px; font-size: 11px; text-decoration: underline; }
    .section .body { font-size: 11px; line-height: 1.4; min-height: 16px; white-space: pre-wrap; }
    .chk { display: inline-block; border: 1px solid #000; width: 11px; height: 11px; text-align: center; line-height: 11px; font-size: 9px; }
    .sign { width: 100%; border: 1px solid #000; margin-top: 6px; }
    .sign td { border: 1px solid #000; text-align: center; height: 70px; vertical-align: top; padding-top: 4px; font-size: 10px; }
    .footer { text-align: center; font-size: 8px; margin-top: 12px; border-top: 1px solid #999; padding-top: 4px; }
    .ftname { font-weight: bold; }

    /* Fishbone halaman 2 */
    .fb-page { page-break-before: always; }
    .fb-title { font-weight: bold; margin: 6px 0 12px; }
    .fb-cat { border: 1px solid #000; padding: 4px 6px; vertical-align: top; width: 33%; }
    .fb-cat .cat { font-weight: bold; background: #e5e7eb; padding: 2px 4px; text-align: center; margin-bottom: 3px; font-size: 10px; }
    .fb-cat ul { margin: 0; padding-left: 14px; }
    .fb-cat li { font-size: 10px; margin-bottom: 2px; }
    .fb-effect { border: 2px solid #000; background: #fde68a; text-align: center; font-weight: bold; padding: 10px; font-size: 12px; }
    .muted { color: #555; }
</style>
</head>
<body>
<div class="wrap">

    {{-- ===== HEADER ===== --}}
    <table class="hdr"><tr>
        <td style="width: 55%; vertical-align: middle;">
            <img src="{{ public_path('images/logowb.png') }}" style="height: 70px;">
        </td>
        <td style="width: 45%; text-align: right;">
            <table class="docbox" align="right">
                <tr><td>No Doc</td><td>: WBN/08-QA</td></tr>
                <tr><td>Issued</td><td>: {{ optional($c->tanggal_complain)->format('d F Y') ?? '-' }}</td></tr>
                <tr><td>Revisi</td><td>: 01</td></tr>
            </table>
        </td>
    </tr></table>

    <div style="margin-top: 8px;">
        <table style="width:100%"><tr>
            <td style="width:78%">
                <div class="title">NON CONFORMING REPORT (NCR)</div>
                <div class="subtitle">No: {{ $noNcr }}</div>
            </td>
            <td style="width:22%; font-size:10px;">
                <span class="chk"></span> Internal<br>
                <span class="chk">✓</span> External
            </td>
        </tr></table>
    </div>

    {{-- ===== INFO ===== --}}
    <table class="infotbl">
        <tr><td class="lbl">Customer</td><td>{{ $c->nama_customer }}</td></tr>
        <tr><td class="lbl">Complaint Date</td><td>{{ optional($c->tanggal_complain)->format('d F Y') ?? '-' }}</td></tr>
        <tr><td class="lbl">Product Size</td><td>{{ $c->ukuran ?: '-' }}</td></tr>
        <tr><td class="lbl">Qty NCR</td><td>{{ $c->qty ? number_format($c->qty) . ' pcs' : '-' }}</td></tr>
    </table>

    {{-- ===== DESKRIPSI ===== --}}
    <div class="section">
        <h4>Deskripsi Ketidaksesuaian</h4>
        <div class="body">{{ $deskripsi ?: '-' }}</div>
    </div>

    <div class="section">
        <h4>FISHBONE: (external)</h4>
        <div class="body">Terlampir Lampiran 1</div>
    </div>

    {{-- ===== PENYEBAB ===== --}}
    <div class="section">
        <h4>Penyebab / root cause:</h4>
        <div class="body">{{ $penyebab ?: '-' }}</div>
    </div>

    {{-- ===== TINDAKAN ===== --}}
    <div class="section">
        <h4>Tindakan Koreksi / Correction Action: (1 x 24 jam)</h4>
        <div class="body">{{ $correction ?: '-' }}</div>
    </div>
    <div class="section">
        <h4>Tindakan Korektif / Corrective Action: (10 x 24 jam)</h4>
        <div class="body">{{ $corrective ?: '-' }}</div>
    </div>

    <div class="section">
        <table style="width:100%"><tr>
            <td>Due Date : {{ optional($c->tanggal_kirim)->format('d F Y') ?? '..............' }}</td>
            <td style="text-align:right;">
                Verifikasi (Maks 1 Bulan) &nbsp;
                <span class="chk">{{ $c->status === 'Open' ? '✓' : '' }}</span> Open
                <span class="chk">{{ $c->status === 'Close' ? '✓' : '' }}</span> Close
                &nbsp; Tanggal: ........
            </td>
        </tr></table>
    </div>

    {{-- ===== TANDA TANGAN ===== --}}
    <table class="sign"><tr>
        <td>QC</td>
        <td>Spv Prod / Spv Qc</td>
        <td>QA</td>
    </tr></table>

    <div class="footer">
        <div class="ftname">PT WAHANA BERMUDA NUSANTARA</div>
        Jl. L. Sukarni kp. Jati Pondok No 111, RT 03/RW 05, DS Tonjong, kec. Tajur Halang - Bogor, Jabar 16230 &middot; P: +62 21 8587 897 &middot; F: +62 21 8587 897
    </div>

    {{-- ===== HALAMAN 2: FISHBONE ===== --}}
    <div class="fb-page">
        <img src="{{ public_path('images/logowb.png') }}" style="height: 70px;">
        <div class="fb-title">Lampiran 1 — Fishbone (6M)</div>

        <table style="width:100%; margin-bottom:10px;"><tr>
            <td style="width:70%; vertical-align:middle;">
                <div class="muted" style="font-size:10px;">Diagram sebab-akibat ketidaksesuaian:</div>
            </td>
            <td style="width:30%;"><div class="fb-effect">Akibat:<br>{{ $efek }}</div></td>
        </tr></table>

        {{-- 3 kategori atas --}}
        <table style="width:100%; margin-bottom:8px;"><tr>
            @foreach (['Man', 'Machine', 'Material'] as $kat)
                <td class="fb-cat">
                    <div class="cat">{{ $fbLabel[$kat] ?? $kat }}</div>
                    @if (! empty($fishbone[$kat]))
                        <ul>@foreach ($fishbone[$kat] as $cause)<li>{{ $cause }}</li>@endforeach</ul>
                    @else <div class="muted" style="font-size:9px; text-align:center;">—</div> @endif
                </td>
            @endforeach
        </tr></table>

        {{-- 3 kategori bawah --}}
        <table style="width:100%;"><tr>
            @foreach (['Method', 'Environment', 'Measurement'] as $kat)
                <td class="fb-cat">
                    <div class="cat">{{ $fbLabel[$kat] ?? $kat }}</div>
                    @if (! empty($fishbone[$kat]))
                        <ul>@foreach ($fishbone[$kat] as $cause)<li>{{ $cause }}</li>@endforeach</ul>
                    @else <div class="muted" style="font-size:9px; text-align:center;">—</div> @endif
                </td>
            @endforeach
        </tr></table>

        <div class="footer">
            <div class="ftname">PT WAHANA BERMUDA NUSANTARA</div>
            Jl. L. Sukarni kp. Jati Pondok No 111, RT 03/RW 05, DS Tonjong, kec. Tajur Halang - Bogor, Jabar 16230
        </div>
    </div>

</div>
</body>
</html>
