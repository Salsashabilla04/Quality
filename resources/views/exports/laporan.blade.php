<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Analisis Mutu - PT Wahana Bermuda Nusantara</title>
<style>
    * { font-family: DejaVu Sans, sans-serif; box-sizing: border-box; }
    body { font-size: 9.5px; color: #1e293b; margin: 0; padding: 0; line-height: 1.45; }

    @page {
        margin: 28px 32px 30px 32px;
    }
    .page-break-before { page-break-before: always; }
    .no-break { page-break-inside: avoid; }

    /* Kop Surat */
    .hdr-table { width: 100%; border-bottom: 2px solid #2e59d9; padding-bottom: 8px; margin-bottom: 12px; }
    .hdr-table td { vertical-align: middle; }
    .doc-box { border: 1px solid #cbd5e1; font-size: 8px; border-collapse: collapse; }
    .doc-box td { padding: 3px 6px; border: 1px solid #cbd5e1; }

    /* Typography */
    h1 { font-size: 13.5px; font-weight: bold; margin: 0 0 2px 0; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.3px; }
    .company-title { font-size: 11px; font-weight: bold; color: #0f172a; margin: 0; }
    .doc-subtitle { font-size: 8.5px; color: #64748b; margin: 0; }

    /* Section Titles - Tema 1 Warna Biru */
    .section-title {
        font-size: 10px;
        font-weight: bold;
        color: #1e3a8a;
        background: #f0f4ff;
        border-left: 3.5px solid #2e59d9;
        border-top: 1px solid #dbeafe;
        border-right: 1px solid #dbeafe;
        border-bottom: 1px solid #dbeafe;
        padding: 5px 8px;
        margin: 12px 0 6px 0;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .sub-title {
        font-size: 9px;
        font-weight: bold;
        color: #1e3a8a;
        margin: 8px 0 4px 0;
        border-bottom: 1px solid #bfdbfe;
        padding-bottom: 2px;
    }

    /* Tabel Clean Look - Konsisten 1 Warna Biru */
    table.data-table { width: 100%; border-collapse: collapse; margin-top: 4px; margin-bottom: 8px; }
    table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 4.5px 7px; font-size: 8.5px; text-align: left; }
    table.data-table th { background: #2e59d9; color: #ffffff; font-weight: bold; font-size: 8.5px; text-align: center; }
    table.data-table tr:nth-child(even) { background: #f8fafc; }
    table.data-table tr.total-row { background: #eef2ff; font-weight: bold; color: #1e3a8a; }

    /* Alignment */
    .text-center { text-align: center !important; }
    .text-right { text-align: right !important; }
    .text-left { text-align: left !important; }
    .font-bold { font-weight: bold !important; }
    .text-muted { color: #64748b !important; }

    /* KPI Table Formal Clean (1 Warna Biru) */
    .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .kpi-table td {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        padding: 6px 8px;
        vertical-align: middle;
    }
    .kpi-table .kpi-label { font-size: 7.5px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; margin-bottom: 2px; }
    .kpi-table .kpi-val { font-size: 13px; font-weight: bold; color: #0f172a; line-height: 1.1; }
    .kpi-table .kpi-desc { font-size: 7.5px; color: #64748b; margin-top: 2px; }

    /* Tag / Status Sederhana (Hanya Nuansa Biru / Netral) */
    .tag-vital { font-weight: bold; color: #1e3a8a; }
    .tag-useful { color: #64748b; }

    /* Lembar Pengesahan Formal */
    .sign-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .sign-table td {
        width: 33.33%;
        border: 1px solid #cbd5e1;
        text-align: center;
        padding: 6px 8px;
        vertical-align: top;
    }
    .sign-title { font-size: 8.5px; font-weight: bold; color: #334155; margin-bottom: 45px; text-transform: uppercase; }
    .sign-name { font-size: 9px; font-weight: bold; color: #0f172a; text-decoration: underline; }
    .sign-role { font-size: 8px; color: #64748b; margin-top: 2px; }

    .doc-footer {
        margin-top: 12px;
        border-top: 1px solid #cbd5e1;
        padding-top: 4px;
        font-size: 7.5px;
        color: #94a3b8;
        text-align: center;
    }
</style>
</head>
<body>

    {{-- ========================================================================= --}}
    {{-- HALAMAN 1: KOP RESMI + RINGKASAN KPI + PARETO JENIS KETIDAKSESUAIAN     --}}
    {{-- ========================================================================= --}}

    {{-- KOP SURAT FORMAL RESMI --}}
    <table class="hdr-table">
        <tr>
            <td style="width: 55%;">
                @if(file_exists(public_path('images/logowb.png')))
                    <img src="{{ public_path('images/logowb.png') }}" style="height: 48px;" alt="Logo PT WBN">
                @else
                    <div style="font-size: 15px; font-weight: bold; color: #1e3a8a;">PT WAHANA BERMUDA NUSANTARA</div>
                @endif
                <div class="company-title" style="margin-top: 3px;">PT WAHANA BERMUDA NUSANTARA</div>
                <div class="doc-subtitle">Quality Assurance &amp; Quality Control Department · Laporan Evaluasi Mutu Resmi</div>
            </td>
            <td style="width: 45%; text-align: right;">
                <table class="doc-box" align="right">
                    <tr>
                        <td style="background: #f8fafc; font-weight: bold; width: 42%;">No. Dokumen</td>
                        <td style="font-weight: bold; color: #1e3a8a;">WBN/QA-QC/LAP/{{ date('Y/m') }}</td>
                    </tr>
                    <tr>
                        <td style="background: #f8fafc; font-weight: bold;">Tanggal Terbit</td>
                        <td>{{ $tanggal }}</td>
                    </tr>
                    <tr>
                        <td style="background: #f8fafc; font-weight: bold;">Periode Data</td>
                        <td>
                            @if(!empty($filterInfo['bulan']) && !empty($filterInfo['tahun']))
                                {{ $filterInfo['bulan'] }} {{ $filterInfo['tahun'] }}
                            @elseif(!empty($filterInfo['tahun']))
                                Tahun {{ $filterInfo['tahun'] }}
                            @else
                                Seluruh Data Terdaftar (All Time)
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="background: #f8fafc; font-weight: bold;">Filter Khusus</td>
                        <td>{{ $filterInfo['customer'] ? 'Customer: ' . $filterInfo['customer'] : ($filterInfo['status'] ? 'Status: ' . $filterInfo['status'] : 'Semua Rekaman') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-bottom: 12px;">
        <h1>LAPORAN ANALISIS MUTU: EVALUASI PARETO &amp; DATA MINING APRIORI</h1>
        <div class="doc-subtitle">
            Ringkasan Pengendalian Mutu, Prioritas Masalah (Prinsip 80/20), dan Pola Asosiasi Cacat
        </div>
    </div>

    {{-- BAGIAN 1: KPI (CLEAN FORMAL, 1 TEMA WARNA BIRU, TANPA LEAD TIME) --}}
    <div class="section-title">1. Ringkasan Key Performance Indicators (KPI Mutu)</div>
    <table class="kpi-table">
        <tr>
            <td style="width: 25%; border-left: 3.5px solid #2e59d9;">
                <div class="kpi-label">Total Complaint</div>
                <div class="kpi-val">{{ $kpi['total_complaint'] }} <span style="font-size: 8px; font-weight: normal; color: #64748b;">Kasus</span></div>
                <div class="kpi-desc">Bulan ini: <b>{{ $kpi['this_month'] }}</b> kasus</div>
            </td>
            <td style="width: 25%; border-left: 3.5px solid #2e59d9;">
                <div class="kpi-label">Status Penyelesaian</div>
                <div class="kpi-val">{{ $kpi['close'] }} <span style="font-size: 8px; font-weight: normal; color: #64748b;">Close ({{ $kpi['persen_close'] }}%)</span></div>
                <div class="kpi-desc">Pending/Open: <b>{{ $kpi['open'] }}</b> kasus</div>
            </td>
            <td style="width: 25%; border-left: 3.5px solid #2e59d9;">
                <div class="kpi-label">Total Kuantitas NG</div>
                <div class="kpi-val">{{ number_format($kpi['total_qty']) }} <span style="font-size: 8px; font-weight: normal; color: #64748b;">Pcs</span></div>
                <div class="kpi-desc">Akumulasi volume barang cacat</div>
            </td>
            <td style="width: 25%; border-left: 3.5px solid #2e59d9;">
                <div class="kpi-label">Customer Terdampak</div>
                <div class="kpi-val">{{ $kpi['jumlah_customer'] }} <span style="font-size: 8px; font-weight: normal; color: #64748b;">Pelanggan</span></div>
                <div class="kpi-desc">Jumlah customer aktif komplain</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-left: 3.5px solid #2e59d9;">
                <div class="kpi-label">Jenis Ketidaksesuaian Tertinggi (Top Defect)</div>
                <div class="kpi-val" style="font-size: 11px;">{{ $kpi['top_defect'] ?: 'Belum ada data' }}</div>
                <div class="kpi-desc">Frekuensi kemunculan: <b>{{ $kpi['top_defect_n'] ?: 0 }}</b> kali kasus</div>
            </td>
            <td colspan="2" style="border-left: 3.5px solid #2e59d9;">
                <div class="kpi-label">Akar Penyebab Dominan (Top Root Cause)</div>
                <div class="kpi-val" style="font-size: 11px;">{{ $kpi['top_cause'] ?: 'Belum ada data' }}</div>
                <div class="kpi-desc">Frekuensi kemunculan: <b>{{ $kpi['top_cause_n'] ?: 0 }}</b> kali kejadian</div>
            </td>
        </tr>
    </table>

    {{-- BAGIAN 2.1: TABEL PARETO JENIS KETIDAKSESUAIAN --}}
    <div class="section-title">2. Analisis Prioritas Masalah — Diagram Pareto (Prinsip 80/20)</div>
    <div class="sub-title">2.1. Tabel Pareto: Jenis Ketidaksesuaian Utama</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 44%; text-align: left;">Jenis Ketidaksesuaian</th>
                <th style="width: 14%;">Frekuensi (Kasus)</th>
                <th style="width: 14%;">Persentase (%)</th>
                <th style="width: 14%;">Kumulatif (%)</th>
                <th style="width: 18%;">Klasifikasi Pareto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pareto['labels'] as $idx => $label)
                @php
                    $val = $pareto['values'][$idx] ?? 0;
                    $cum = $pareto['cumulative'][$idx] ?? 0;
                    $pct = $pareto['total'] > 0 ? round(($val / $pareto['total']) * 100, 1) : 0;
                    $isVital = $cum <= 80 || ($idx > 0 && ($pareto['cumulative'][$idx-1] ?? 0) < 80);
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $label }}</td>
                    <td class="text-center font-bold">{{ $val }}</td>
                    <td class="text-right">{{ $pct }}%</td>
                    <td class="text-right font-bold">{{ $cum }}%</td>
                    <td class="text-center">
                        @if ($isVital)
                            <span class="tag-vital">Vital Few (Prioritas 80%)</span>
                        @else
                            <span class="tag-useful">Useful Many</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">Tidak ada data jenis ketidaksesuaian.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ $pareto['total'] }}</td>
                <td class="text-right">100.0%</td>
                <td class="text-right">100.0%</td>
                <td class="text-center">-</td>
            </tr>
        </tbody>
    </table>

    <div class="doc-footer">
        Halaman 1 · Laporan Analisis Mutu &amp; Apriori · PT Wahana Bermuda Nusantara · Dicetak: {{ $tanggal }}
    </div>

    {{-- ========================================================================= --}}
    {{-- HALAMAN 2: PARETO DETAIL KETIDAKSESUAIAN & PARETO CUSTOMER              --}}
    {{-- ========================================================================= --}}
    <div class="page-break-before"></div>

    <table class="hdr-table">
        <tr>
            <td style="width: 60%;"><div class="company-title">PT WAHANA BERMUDA NUSANTARA · QA/QC REPORT</div></td>
            <td style="width: 40%; text-align: right;"><div class="doc-subtitle">Analisis Pareto: Detail Cacat &amp; Customer</div></td>
        </tr>
    </table>

    {{-- 2.2 TABEL PARETO DETAIL KETIDAKSESUAIAN --}}
    <div class="sub-title" style="margin-top: 0;">2.2. Tabel Pareto: Detail Ketidaksesuaian (Spesifik)</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 44%; text-align: left;">Detail Ketidaksesuaian</th>
                <th style="width: 14%;">Frekuensi (Kasus)</th>
                <th style="width: 14%;">Persentase (%)</th>
                <th style="width: 14%;">Kumulatif (%)</th>
                <th style="width: 18%;">Klasifikasi Pareto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($paretoDetail['labels'] ?? [] as $dIdx => $dLabel)
                @php
                    $dVal = $paretoDetail['values'][$dIdx] ?? 0;
                    $dCum = $paretoDetail['cumulative'][$dIdx] ?? 0;
                    $dPct = ($paretoDetail['total'] ?? 0) > 0 ? round(($dVal / $paretoDetail['total']) * 100, 1) : 0;
                    $isVitalD = $dCum <= 80 || ($dIdx > 0 && ($paretoDetail['cumulative'][$dIdx-1] ?? 0) < 80);
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $dIdx + 1 }}</td>
                    <td class="font-bold">{{ $dLabel }}</td>
                    <td class="text-center font-bold">{{ $dVal }}</td>
                    <td class="text-right">{{ $dPct }}%</td>
                    <td class="text-right font-bold">{{ $dCum }}%</td>
                    <td class="text-center">
                        @if ($isVitalD)
                            <span class="tag-vital">Vital Few (Prioritas 80%)</span>
                        @else
                            <span class="tag-useful">Useful Many</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">Tidak ada data detail ketidaksesuaian.</td></tr>
            @endforelse
            @if(($paretoDetail['total'] ?? 0) > 0)
                <tr class="total-row">
                    <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-center">{{ $paretoDetail['total'] }}</td>
                    <td class="text-right">100.0%</td>
                    <td class="text-right">100.0%</td>
                    <td class="text-center">-</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- 2.3 TABEL PARETO CUSTOMER --}}
    <div class="sub-title" style="margin-top: 14px;">2.3. Tabel Pareto: Peringkat Customer Terbanyak Komplain</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 44%; text-align: left;">Nama Perusahaan / Customer</th>
                <th style="width: 14%;">Frekuensi Komplain</th>
                <th style="width: 14%;">Persentase (%)</th>
                <th style="width: 14%;">Kumulatif (%)</th>
                <th style="width: 18%;">Prioritas Penanganan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($paretoCustomer['labels'] ?? [] as $cIdx => $cName)
                @php
                    $cVal = $paretoCustomer['values'][$cIdx] ?? 0;
                    $cCum = $paretoCustomer['cumulative'][$cIdx] ?? 0;
                    $cPct = ($paretoCustomer['total'] ?? 0) > 0 ? round(($cVal / $paretoCustomer['total']) * 100, 1) : 0;
                    $isTopCust = $cCum <= 80 || $cIdx < 3;
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $cIdx + 1 }}</td>
                    <td class="font-bold">{{ $cName }}</td>
                    <td class="text-center font-bold">{{ $cVal }}</td>
                    <td class="text-right">{{ $cPct }}%</td>
                    <td class="text-right font-bold">{{ $cCum }}%</td>
                    <td class="text-center">
                        @if ($isTopCust)
                            <span class="tag-vital">Prioritas Utama</span>
                        @else
                            <span class="tag-useful">Reguler</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">Tidak ada data customer komplain.</td></tr>
            @endforelse
            @if(($paretoCustomer['total'] ?? 0) > 0)
                <tr class="total-row">
                    <td colspan="2" class="text-center">TOTAL KOMPLAIN CUSTOMER</td>
                    <td class="text-center">{{ $paretoCustomer['total'] }}</td>
                    <td class="text-right">100.0%</td>
                    <td class="text-right">100.0%</td>
                    <td class="text-center">-</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="doc-footer">
        Halaman 2 · Laporan Analisis Mutu &amp; Apriori · PT Wahana Bermuda Nusantara · Dicetak: {{ $tanggal }}
    </div>

    {{-- ========================================================================= --}}
    {{-- HALAMAN 3: SELURUH FREQUENT ITEMSETS ALGORITMA APRIORI                  --}}
    {{-- ========================================================================= --}}
    <div class="page-break-before"></div>

    <table class="hdr-table">
        <tr>
            <td style="width: 60%;"><div class="company-title">PT WAHANA BERMUDA NUSANTARA · QA/QC REPORT</div></td>
            <td style="width: 40%; text-align: right;"><div class="doc-subtitle">Data Mining: Algoritma Apriori (Frequent Itemsets)</div></td>
        </tr>
    </table>

    {{-- BAGIAN 3: ANALISIS ALGORITMA APRIORI LENGKAP --}}
    <div class="section-title">3. Analisis Pola Asosiasi Cacat Menggunakan Algoritma Apriori</div>

    {{-- PARAMETER TABEL FORMAL CLEAN --}}
    <table class="data-table" style="margin-bottom: 8px;">
        <thead>
            <tr>
                <th style="width: 25%;">Total Transaksi Analisis</th>
                <th style="width: 25%;">Minimum Support</th>
                <th style="width: 25%;">Minimum Confidence</th>
                <th style="width: 25%;">Total Aturan Asosiasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center font-bold">{{ $totalTrx }} Data</td>
                <td class="text-center font-bold">{{ $minSupport }}%</td>
                <td class="text-center font-bold">{{ $minConfidence }}%</td>
                <td class="text-center font-bold">{{ count($rules) }} Aturan Valid</td>
            </tr>
        </tbody>
    </table>

    {{-- 3.1 SELURUH FREQUENT ITEMSETS --}}
    <div class="sub-title">3.1. Seluruh Hasil Frequent Itemsets (Pola Kemunculan Item Sering)</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 54%; text-align: left;">Kombinasi Itemset (Cacat &amp; Penyebab)</th>
                <th style="width: 14%;">Ukuran Itemset</th>
                <th style="width: 13%;">Support (%)</th>
                <th style="width: 13%;">Frekuensi Kasus</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($frequent as $fi => $f)
                @php
                    $cleanItems = array_map(fn($item) => str_replace(['Ketidaksesuaian=', 'Penyebab='], '', $item), $f['items']);
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $fi + 1 }}</td>
                    <td class="font-bold">{{ implode('  +  ', $cleanItems) }}</td>
                    <td class="text-center">{{ $f['size'] }}-itemset</td>
                    <td class="text-right font-bold">{{ number_format($f['support'] * 100, 2) }}%</td>
                    <td class="text-center font-bold">{{ $f['count'] }} kali</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">Tidak ditemukan frequent itemset yang memenuhi ambang minimum support.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="doc-footer">
        Halaman 3 · Laporan Analisis Mutu &amp; Apriori · PT Wahana Bermuda Nusantara · Dicetak: {{ $tanggal }}
    </div>

    {{-- ========================================================================= --}}
    {{-- HALAMAN 4: SELURUH ATURAN ASOSIASI, REKOMENDASI CAPA & PENGESAHAN       --}}
    {{-- ========================================================================= --}}
    <div class="page-break-before"></div>

    <table class="hdr-table">
        <tr>
            <td style="width: 60%;"><div class="company-title">PT WAHANA BERMUDA NUSANTARA · QA/QC REPORT</div></td>
            <td style="width: 40%; text-align: right;"><div class="doc-subtitle">Data Mining: Association Rules &amp; Pengesahan</div></td>
        </tr>
    </table>

    {{-- 3.2 SELURUH ATURAN ASOSIASI --}}
    <div class="sub-title" style="margin-top: 0;">3.2. Seluruh Aturan Asosiasi Final (Association Rules)</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 27%; text-align: left;">Jika Kejadian (Antecedent)</th>
                <th style="width: 27%; text-align: left;">Maka Akibat (Consequent)</th>
                <th style="width: 10%;">Support</th>
                <th style="width: 11%;">Confidence</th>
                <th style="width: 10%;">Lift Ratio</th>
                <th style="width: 10%;">Korelasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rules as $ri => $rule)
                <tr>
                    <td class="text-center font-bold">{{ $ri + 1 }}</td>
                    <td class="font-bold">{{ $rule['antecedents'] }}</td>
                    <td class="font-bold">{{ $rule['consequents'] }}</td>
                    <td class="text-right">{{ number_format($rule['support'] * 100, 1) }}%</td>
                    <td class="text-right font-bold">{{ number_format($rule['confidence'] * 100, 1) }}%</td>
                    <td class="text-center font-bold">{{ number_format($rule['lift'], 2) }}</td>
                    <td class="text-center">
                        {{ $rule['kekuatan'] ?? 'Kuat' }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">Tidak ditemukan aturan asosiasi yang memenuhi syarat confidence minimum.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- 3.3 INTERPRETASI & REKOMENDASI TINDAKAN KOREKTIF-PREVENTIF (CAPA) --}}
    <div class="sub-title" style="margin-top: 10px;">3.3. Interpretasi Hasil &amp; Rekomendasi Rencana Tindakan (Action Plan)</div>
    @if(count($rules))
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 24%; text-align: left;">Pola Aturan Asosiasi</th>
                    <th style="width: 70%; text-align: left;">Interpretasi &amp; Rekomendasi Tindakan Mutu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rules as $rIdx => $r)
                    <tr>
                        <td class="text-center font-bold">{{ $rIdx + 1 }}</td>
                        <td class="font-bold">{{ $r['antecedents'] }} &rarr; {{ $r['consequents'] }}</td>
                        <td>
                            {{ $r['interpretasi'] }}
                            <div class="text-muted" style="margin-top: 2px; font-size: 8px;">
                                Tingkat Keyakinan: <b>{{ number_format($r['confidence'] * 100, 1) }}%</b> | Nilai Lift: <b>{{ number_format($r['lift'], 2) }}</b>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 8.5px; color: #64748b; margin-bottom: 10px;">
            Belum ada pola asosiasi yang memenuhi ambang batas filter. Rekomendasi tindakan difokuskan pada hasil eliminasi cacat pada Tabel Pareto Jenis Ketidaksesuaian (Prioritas Vital Few 80%).
        </div>
    @endif

    {{-- BAGIAN 4: LEMBAR PENGESAHAN DOKUMEN RESMI (TANDA TANGAN) --}}
    <div class="no-break" style="margin-top: 12px;">
        <div style="font-size: 9px; font-weight: bold; color: #1e3a8a; margin-bottom: 4px; text-transform: uppercase;">4. Lembar Pengesahan Laporan Mutu</div>
        <table class="sign-table">
            <tr>
                <td>
                    <div class="sign-title">Dibuat Oleh:</div>
                    <div class="sign-name">Staff Quality Assurance (QA)</div>
                    <div class="sign-role">Petugas Pengolah Data &amp; Analisis Mutu</div>
                </td>
                <td>
                    <div class="sign-title">Diperiksa &amp; Disetujui Oleh:</div>
                    <div class="sign-name">Supervisor Quality Control</div>
                    <div class="sign-role">Verifikasi Temuan &amp; Validasi CAPA</div>
                </td>
                <td>
                    <div class="sign-title">Mengetahui:</div>
                    <div class="sign-name">Manager Quality Assurance / Plant</div>
                    <div class="sign-role">Penanggung Jawab Sistem Manajemen Mutu</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-footer">
        Halaman 4 · Laporan Analisis Mutu &amp; Apriori · PT Wahana Bermuda Nusantara · Dicetak: {{ $tanggal }}
    </div>

</body>
</html>
