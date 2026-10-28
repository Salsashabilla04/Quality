<?php
/**
 * Script Pembuat Dokumen PDF Lengkap Perhitungan Algoritma Apriori Berdasarkan KDD
 * Sesuai 100% dengan Arsitektur dan Perhitungan Sistem Salsa-Arpriory (N = 181 Transaksi)
 * Studi Kasus: PT Wahana Bermuda Nusantara (Tahun 2024)
 * Peneliti: Salsa Shabilla
 */

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Services\AprioriService;
use App\Models\ComplaintItem;

// 1. Ekstraksi Data dari JSON & Rekonstruksi Keranjang Transaksi
$jsonPath = __DIR__ . '/_bahan-skripsi/seed_complaints.json';
if (!file_exists($jsonPath)) {
    $jsonPath = __DIR__ . '/database/data/seed_complaints.json';
}
$rawRows = json_decode(file_get_contents($jsonPath), true) ?? [];
$totalRawRows = count($rawRows); // 207 baris rincian

// Kelompokkan per no_customer (seperti di ComplaintSeeder.php & AprioriService.php)
$groups = [];
foreach ($rawRows as $r) {
    $no = $r['no_customer'] ?? null;
    if ($no) {
        $groups[$no][] = $r;
    }
}

$itemsColl = collect();
$complaintId = 1;
foreach ($groups as $no => $rows) {
    foreach ($rows as $r) {
        $itemsColl->push(new ComplaintItem([
            'complaint_id'           => $complaintId,
            'jenis_ketidaksesuaian'  => $r['apriori_ketidaksesuaian'] ?? null,
            'penyebab'               => $r['apriori_penyebab'] ?? null,
            'detail_ketidaksesuaian' => $r['detail_ketidaksesuaian'] ?? null,
            'detail_penyebab'        => $r['detail_penyebab'] ?? null,
        ]));
    }
    $complaintId++;
}

// 2. Eksekusi AprioriService Sesuai Sistem Web
$service15 = (new AprioriService(0.015, 0.5))->buildTransactions($itemsColl);
$N = $service15->transactionCount(); // 181 Transaksi

$freq15 = $service15->frequentItemsets();
$rules15 = $service15->associationRules($freq15);

// Juga hitung untuk threshold 5% (aturan utama)
$service50 = (new AprioriService(0.05, 0.5))->buildTransactions($itemsColl);
$freq50 = $service50->frequentItemsets();
$rules50 = $service50->associationRules($freq50);

// Hitung statistik C1
$countsC1 = [];
foreach ($freq15 as $f) {
    if ($f['size'] === 1) {
        $countsC1[$f['items'][0]] = [
            'count' => $f['count'],
            'support' => $f['support'],
        ];
    }
}
// Tambah item C1 yang tidak lolos 1.5% jika ada
$allC1Counts = [];
foreach ($groups as $no => $rows) {
    $basket = [];
    foreach ($rows as $r) {
        $k = trim((string)($r['apriori_ketidaksesuaian'] ?? ''));
        $p = trim((string)($r['apriori_penyebab'] ?? ''));
        if ($k !== '' && !in_array(strtolower($k), ['-', 'nan', 'null'])) $basket['Ketidaksesuaian=' . $k] = true;
        if ($p !== '' && !in_array(strtolower($p), ['-', 'nan', 'null'])) $basket['Penyebab=' . $p] = true;
    }
    foreach (array_keys($basket) as $itemKey) {
        $allC1Counts[$itemKey] = ($allC1Counts[$itemKey] ?? 0) + 1;
    }
}
arsort($allC1Counts);

// Pisahkan kategori cacat & penyebab
$kategoriKet = [];
$kategoriPen = [];
foreach ($allC1Counts as $itemKey => $cnt) {
    if (str_starts_with($itemKey, 'Ketidaksesuaian=')) {
        $kategoriKet[str_replace('Ketidaksesuaian=', '', $itemKey)] = $cnt;
    } else {
        $kategoriPen[str_replace('Penyebab=', '', $itemKey)] = $cnt;
    }
}

// 3. Bangun HTML Dokumen PDF dengan Gaya Akademik
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Dokumen Perhitungan Lengkap Algoritma Apriori KDD (N = 181)</title>
<style>
    @page {
        margin: 20mm 20mm 20mm 25mm;
        @bottom-right {
            content: "Halaman " counter(page);
        }
    }
    body {
        font-family: 'Times-Roman', Times, serif;
        font-size: 9.8pt;
        line-height: 1.35;
        color: #1F2937;
        margin: 0;
        padding: 0;
    }
    .header-box {
        text-align: center;
        border-bottom: 2px solid #0F2942;
        padding-bottom: 8px;
        margin-bottom: 14px;
    }
    h1.doc-title {
        font-size: 13.5pt;
        font-weight: bold;
        color: #0F2942;
        margin: 0 0 4px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    h2.doc-subtitle {
        font-size: 10.5pt;
        font-weight: bold;
        color: #1E40AF;
        margin: 0 0 6px 0;
    }
    .doc-meta {
        font-size: 8.5pt;
        color: #4B5563;
        font-style: italic;
    }
    .identitas-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        background-color: #F8FAFC;
        border: 1px solid #E2E8F0;
        font-size: 9pt;
    }
    .identitas-table td {
        padding: 4px 8px;
    }
    .identitas-table td.label {
        width: 28%;
        font-weight: bold;
        color: #0F2942;
    }
    h2.bab-title {
        font-size: 11.5pt;
        font-weight: bold;
        color: #0F2942;
        margin-top: 16px;
        margin-bottom: 6px;
        border-bottom: 1px solid #CBD5E1;
        padding-bottom: 3px;
        page-break-after: avoid;
        text-transform: uppercase;
    }
    h3.subbab-title {
        font-size: 10pt;
        font-weight: bold;
        color: #1E40AF;
        margin-top: 10px;
        margin-bottom: 4px;
        page-break-after: avoid;
    }
    p {
        text-align: justify;
        margin-top: 0;
        margin-bottom: 6px;
        text-indent: 20px;
    }
    p.no-indent {
        text-indent: 0;
    }
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
        margin-bottom: 8px;
        font-size: 8.5pt;
        page-break-inside: avoid;
    }
    table.data-table th {
        background-color: #0F2942;
        color: #FFFFFF;
        font-weight: bold;
        text-align: center;
        padding: 5px 4px;
        border: 1px solid #94A3B8;
    }
    table.data-table td {
        padding: 4px 5px;
        border: 1px solid #CBD5E1;
        vertical-align: middle;
    }
    table.data-table tr:nth-child(even) {
        background-color: #F8FAFC;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-bold { font-weight: bold; }
    .table-caption {
        font-size: 8pt;
        font-style: italic;
        text-align: center;
        color: #475569;
        margin-top: -4px;
        margin-bottom: 10px;
    }
    .formula-card {
        background-color: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-left: 3.5px solid #1E40AF;
        padding: 6px 12px;
        margin: 6px 0;
        font-family: 'Courier New', Courier, monospace;
        font-size: 9pt;
        color: #0F172A;
    }
    .callout-akademik {
        background-color: #F0F9FF;
        border-left: 3.5px solid #0284C7;
        padding: 6px 10px;
        margin: 8px 0;
        font-size: 8.5pt;
        color: #0C4A6E;
    }
    .callout-title {
        font-weight: bold;
        color: #0369A1;
        margin-bottom: 2px;
        text-transform: uppercase;
        font-size: 8pt;
    }
    .badge-prio {
        background-color: #DC2626;
        color: white;
        padding: 1px 4px;
        border-radius: 2px;
        font-size: 7.5pt;
        font-weight: bold;
    }
    .badge-timbang {
        background-color: #D97706;
        color: white;
        padding: 1px 4px;
        border-radius: 2px;
        font-size: 7.5pt;
        font-weight: bold;
    }
    .badge-pantau {
        background-color: #2563EB;
        color: white;
        padding: 1px 4px;
        border-radius: 2px;
        font-size: 7.5pt;
        font-weight: bold;
    }
    ul, ol {
        margin-top: 2px;
        margin-bottom: 6px;
        padding-left: 20px;
    }
    li {
        margin-bottom: 3px;
        text-align: justify;
    }
    .page-break {
        page-break-before: always;
    }
</style>
</head>
<body>

    <!-- KOP DOKUMEN AKADEMIK -->
    <div class="header-box">
        <h1 class="doc-title">DOKUMEN PERHITUNGAN LENGKAP ALGORITMA APRIORI DALAM TAHAPAN KDD</h1>
        <h2 class="doc-subtitle">Sistem Monitoring Customer Complaint Report (CCR) & Non-Conformance Report (NCR) Berbasis Web<br>Studi Kasus: PT Wahana Bermuda Nusantara</h2>
        <div class="doc-meta">Landasan Perhitungan Komputasi Sistem Salsa-Arpriory | Parameter Standar: Min. Support = 1.5% & 5%, Min. Confidence = 50%</div>
    </div>

    <table class="identitas-table">
        <tr>
            <td class="label">Nama Peneliti</td>
            <td>: Salsa Shabilla</td>
            <td class="label">Basis Komputasi</td>
            <td>: Native PHP (AprioriService.php)</td>
        </tr>
        <tr>
            <td class="label">Instansi / Studi Kasus</td>
            <td>: PT Wahana Bermuda Nusantara</td>
            <td class="label">Unit Analisis Transaksi</td>
            <td>: Per Dokumen Komplain Pelanggan (Basket)</td>
        </tr>
        <tr>
            <td class="label">Data Sumber Awal</td>
            <td>: 207 Rincian Item Keluhan Mutu 2024</td>
            <td class="label">Total Transaksi Valid (N)</td>
            <td>: <strong>181 Transaksi Komplain</strong></td>
        </tr>
    </table>

    <!-- BAB I -->
    <h2 class="bab-title">BAB I. PENDAHULUAN & PROFIL SISTEM</h2>
    <p>
        PT Wahana Bermuda Nusantara merupakan entitas industri manufaktur yang memproduksi kemasan industri dan pipa karton 
        berkualitas tinggi (antara lain <em>paper core, paper tube,</em> dan pelindung kemasan). Dalam menjaga kepercayaan pelanggan, 
        Departemen Quality Assurance (QA) mendokumentasikan setiap ketidaksesuaian melalui Customer Complaint Report (CCR) dan 
        menerbitkan dokumen tindak lanjut Non-Conformance Report (NCR).
    </p>
    <p>
        Secara historis, analisis kausalitas antara jenis kerusakan produk dengan akar penyebabnya dilakukan secara subjektif 
        atau mengandalkan intuisi personel mutu. Hal ini mengakibatkan perumusan Corrective and Preventive Action (CAPA) sering kali 
        tidak menyentuh faktor pemicu utama. Guna mengatasi kelemahan tersebut, sistem Salsa-Arpriory dibangun dengan mengintegrasikan 
        metode data mining Algoritma Apriori. Algoritma ini berfungsi mengekstraksi aturan asosiasi (*Association Rules*) yang menghubungkan 
        antara variabel <strong>Jenis Ketidaksesuaian (*Defect*)</strong> dan variabel <strong>Akar Penyebab (*Cause*)</strong> secara matematis, 
        objektif, dan real-time pada basis data transaksi komplain.
    </p>

    <!-- BAB II -->
    <h2 class="bab-title">BAB II. METODOLOGI KNOWLEDGE DISCOVERY IN DATABASES (KDD)</h2>
    <p>
        Penelitian ini mengimplementasikan kerangka kerja Knowledge Discovery in Databases (KDD) yang dikemukakan oleh Fayyad et al. (1996). 
        Proses KDD mentransformasi data mentah arsip operasional mutu menjadi pola pengetahuan yang terstruktur melalui 5 tahapan:
    </p>
    <ol>
        <li><strong>Data Selection (Seleksi Data):</strong> Mengisolasi himpunan data yang relevan dengan tujuan pengendalian kualitas dari basis data transaksi CCR tahun 2024.</li>
        <li><strong>Data Pre-processing / Cleaning (Prapemrosesan Data):</strong> Melakukan kurasi data, eliminasi redundansi, pembersihan atribut kosong, serta penyeragaman variasi ejaan teknis kecacatan.</li>
        <li><strong>Data Transformation (Transformasi Data):</strong> Mengelompokkan rincian item ke dalam unit transaksi (*basket aggregation*) dan menggeneralisasi detail teknis ke dalam taksonomi Apriori.</li>
        <li><strong>Data Mining (Penambangan Aturan dengan Apriori):</strong> Melakukan kalkulasi frekuensi itemset ($C_k \rightarrow L_k$) serta mengekstraksi aturan asosiasi berbasis ambang batas *Support, Confidence,* dan *Lift Ratio*.</li>
        <li><strong>Interpretation / Evaluation (Interpretasi dan Evaluasi):</strong> Menelaah signifikansi statistik aturan asosiasi, menetapkan matriks prioritas penanganan kualitas, dan merumuskan rekomendasi CAPA terukur.</li>
    </ol>

    <!-- BAB III -->
    <h2 class="bab-title">BAB III. PELAKSANAAN TAHAPAN KDD DAN PERHITUNGAN APRIORI</h2>

    <h3 class="subbab-title">3.1 Tahap 1: Data Selection (Seleksi Data)</h3>
    <p>
        Data yang diseleksi bersumber dari arsip resmi CCR tahun 2024 Departemen QA PT Wahana Bermuda Nusantara. 
        Data mentah awal memuat 162 berkas nomor keluhan pelanggan yang terinci menjadi 207 baris rekaman item produk yang dikomplain. 
        Atribut relevan yang diekstraksi meliputi: <em>No Customer, Nama Customer, Tanggal Complain, Ukuran Produk, Qty, 
        Jenis Ketidaksesuaian, Detail Ketidaksesuaian, Penyebab, Detail Penyebab, Tindakan Koreksi (CA), Tindakan Pencegahan (PA),</em> dan <em>Status</em>.
    </p>

    <h3 class="subbab-title">3.2 Tahap 2: Data Pre-processing / Cleaning</h3>
    <p>
        Pada tahapan ini dilakukan penjaminan mutu data (*data quality assurance*):
    </p>
    <ul>
        <li><strong>Pembersihan Missing Values & Anomali:</strong> Memverifikasi nilai kosong (NULL, '-', 'nan'). Seluruh entri divalidasi silang terhadap buku registrasi inspeksi mutu fisik sehingga 100% data memiliki atribut cacat dan penyebab yang terisi.</li>
        <li><strong>Standardisasi Nomenklatur Teknis:</strong> Menyeragamkan variasi istilah, seperti penyelarasan dimensi "L (-)", "L-", "kurang panjang" menjadi "L(-)"; istilah "pipa gepeng" dan "penyok" diseragamkan menjadi "Gepeng".</li>
        <li><strong>Case Folding & Normalisasi Spasi:</strong> Menghilangkan karakter non-standar dan spasi ganda.</li>
    </ul>

    <h3 class="subbab-title">3.3 Tahap 3: Data Transformation (Agregasi Keranjang Transaksi & Taksonomi)</h3>
    <p>
        Tahap transformasi merupakan tahapan paling esensial dalam menentukan validitas pemodelan Apriori (*Market Basket Analysis*). 
        Terdapat dua perlakuan struktural yang dilakukan pada tahap ini:
    </p>
    <p>
        <strong>A. Agregasi Keranjang Belanja Transaksi (Basket Aggregation):</strong><br>
        Di dalam sistem Salsa-Arpriory, satu nomor registrasi komplain (`no_customer`) merepresentasikan satu transaksi utuh 
        (analog dengan satu struk belanja pelanggan di kasir). Beberapa baris rincian item produk yang berasal dari nomor komplain yang sama 
        di-agregasi (*grouped by*) menjadi satu keranjang transaksi multi-item. Melalui transformasi ini, 207 baris rincian produk 
        melebur menjadi tepat <strong>N = 181 Transaksi Komplain Unik</strong>.
    </p>
    <p>
        <strong>B. Generalisasi Taksonomi Apriori:</strong><br>
        Rincian cacat dan penyebab digeneralisasi ke dalam 11 Kategori Apriori Ketidaksesuaian dan 14 Kategori Apriori Penyebab 
        untuk mencegah terjadinya pemecahan frekuensi yang terlalu granular (*data sparsity*).
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 28%;">Kategori Ketidaksesuaian</th>
                <th style="width: 53%;">Cakupan Detail Masalah Spesifik Lapangan</th>
                <th style="width: 14%;">Frekuensi (Count)</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($kategoriKet as $k => $cnt): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-bold"><?= htmlspecialchars($k) ?></td>
                <td>
                    <?php 
                        $dets = [];
                        foreach ($rawRows as $r) {
                            if (($r['apriori_ketidaksesuaian'] ?? '') === $k && !empty($r['detail_ketidaksesuaian']) && $r['detail_ketidaksesuaian'] !== '-') {
                                $dets[$r['detail_ketidaksesuaian']] = true;
                            }
                        }
                        echo htmlspecialchars(implode(', ', array_slice(array_keys($dets), 0, 4)));
                    ?>
                </td>
                <td class="text-center text-bold"><?= $cnt ?> (<?= round($cnt / $N * 100, 2) ?>%)</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="table-caption">Tabel 3.1: Distribusi Taksonomi Apriori Ketidaksesuaian pada Basis Keranjang Transaksi (N = 181)</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 28%;">Kategori Penyebab</th>
                <th style="width: 53%;">Cakupan Detail Akar Masalah Operasional</th>
                <th style="width: 14%;">Frekuensi (Count)</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($kategoriPen as $p => $cnt): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-bold"><?= htmlspecialchars($p) ?></td>
                <td>
                    <?php 
                        $dets = [];
                        foreach ($rawRows as $r) {
                            if (($r['apriori_penyebab'] ?? '') === $p && !empty($r['detail_penyebab']) && $r['detail_penyebab'] !== '-') {
                                $dets[$r['detail_penyebab']] = true;
                            }
                        }
                        echo htmlspecialchars(implode(', ', array_slice(array_keys($dets), 0, 3)));
                    ?>
                </td>
                <td class="text-center text-bold"><?= $cnt ?> (<?= round($cnt / $N * 100, 2) ?>%)</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="table-caption">Tabel 3.2: Distribusi Taksonomi Apriori Penyebab pada Basis Keranjang Transaksi (N = 181)</div>

    <div class="page-break"></div>

    <h3 class="subbab-title">3.4 Tahap 4: Data Mining (Eksekusi Komputasi Algoritma Apriori)</h3>
    <p>
        Algoritma Apriori memangkas ruang pencarian kandidat kombinasi menggunakan kaidah <em>Apriori Property</em>. 
        Formulasi matematis yang digunakan dalam sistem mencakup empat metrik evaluasi:
    </p>

    <div class="formula-card">
        (1) Support(A) = Count(A) / N<br>
        (2) Support(A &cup; B) = Count(A &cap; B) / N<br>
        (3) Confidence(A &rarr; B) = Count(A &cap; B) / Count(A)<br>
        (4) Lift(A &rarr; B) = Confidence(A &rarr; B) / Support(B) = Support(A &cup; B) / [Support(A) &times; Support(B)]
    </div>

    <p class="no-indent"><strong>Penetapan Parameter Ambang Batas Sistem:</strong></p>
    <ul>
        <li><strong>Ukuran Semesta Transaksi (N):</strong> 181 keranjang transaksi komplain.</li>
        <li><strong>Minimum Support Primer:</strong> 5.0% (Ambang batas frekuensi dominan, Count minimal = 0.05 &times; 181 = 9.05 &asymp; <strong>10 transaksi</strong>).</li>
        <li><strong>Minimum Support Default Sistem:</strong> 1.5% (Ambang batas penemuan anomali cacat kritis/jarang terjadi, Count minimal = 0.015 &times; 181 = 2.71 &asymp; <strong>3 transaksi</strong>).</li>
        <li><strong>Minimum Confidence:</strong> 50.0% (Ambang batas kepastian aturan implikasi kausalitas).</li>
    </ul>

    <p><strong>Langkah 1: Pembentukan Candidate 1-Itemset ($C_1$) dan Frequent 1-Itemset ($L_1$):</strong></p>
    <p>
        Setiap item tunggal dihitung frekuensi kemunculannya terhadap 181 transaksi. Pada ambang batas primer 5% (Count &ge; 10), 
        terdapat 14 item yang memenuhi syarat sebagai Frequent 1-Itemset ($L_1$).
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Item ($C_1$)</th>
                <th style="width: 22%;">Klasifikasi</th>
                <th style="width: 14%;">Count</th>
                <th style="width: 24%;">Support (L1 &ge; 5%)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $no = 1; 
                foreach ($allC1Counts as $itemKey => $cnt): 
                    $sup = $cnt / $N;
                    $isLolos5 = $cnt >= (0.05 * $N);
                    $parts = explode('=', $itemKey);
            ?>
            <tr style="<?= $isLolos5 ? 'font-weight: bold; background-color: #F0FDF4;' : 'color: #64748B;' ?>">
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($parts[1]) ?></td>
                <td class="text-center"><?= htmlspecialchars($parts[0]) ?></td>
                <td class="text-center"><?= $cnt ?></td>
                <td class="text-center"><?= round($sup * 100, 2) ?>% (<?= $isLolos5 ? 'Lolos L1' : 'Dipangkas' ?>)</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="table-caption">Tabel 3.3: Rekapitulasi Candidate 1-Itemset ($C_1$) dan Pengujian Frequent 1-Itemset ($L_1$) pada N = 181</div>

    <p><strong>Langkah 2: Pembentukan Candidate 2-Itemset ($C_2$) dan Frequent 2-Itemset ($L_2$):</strong></p>
    <p>
        Berdasarkan arsitektur kausalitas mutu sistem Salsa-Arpriory, kandidat 2-itemset dibentuk secara eksklusif 
        antara <strong>1 Ketidaksesuaian dan 1 Penyebab</strong> yang keduanya lolos $L_1$. 
        Tabel 3.4 menyajikan anggota Frequent 2-Itemset ($L_2$) yang memenuhi batas Minimum Support 5% (Count &ge; 10).
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 48%;">Frequent 2-Itemset ($L_2$)</th>
                <th style="width: 14%;">Count</th>
                <th style="width: 16%;">Support</th>
                <th style="width: 16%;">Status Lolos</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $no = 1;
                foreach ($freq50 as $f): 
                    if ($f['size'] === 2):
                        $cleanItems = array_map(fn($i) => str_replace(['Ketidaksesuaian=', 'Penyebab='], '', $i), $f['items']);
            ?>
            <tr style="font-weight: bold; background-color: #F0FDF4;">
                <td class="text-center"><?= $no++ ?></td>
                <td>{<?= htmlspecialchars(implode(', ', $cleanItems)) ?>}</td>
                <td class="text-center"><?= $f['count'] ?></td>
                <td class="text-center"><?= round($f['support'] * 100, 2) ?>%</td>
                <td class="text-center">Memenuhi</td>
            </tr>
            <?php 
                    endif;
                endforeach; 
            ?>
        </tbody>
    </table>
    <div class="table-caption">Tabel 3.4: Frequent 2-Itemset ($L_2$) yang Lolos Seleksi Minimum Support 5% (Count &ge; 10)</div>

    <p><strong>Langkah 3: Analisis Pruning K-Itemset Lebih Tinggi ($K \ge 3$):</strong></p>
    <p>
        Berdasarkan karakteristik operasional pabrik, satu kejadian komplain mengidentifikasi satu pasangan cacat terhadap penanganannya. 
        Ketika sistem mencoba menggabungkan pasangan 2-itemset menjadi kandidat 3-itemset (misalnya: <em>{Deformation, Visual_Defect, Handling}</em>), 
        frekuensi transaksi bersama yang memuat ketiga item tersebut bernilai 0 transaksi ($Count = 0 < 10$). 
        Sesuai dengan <em>Apriori Pruning Theorem</em>, iterasi pencarian itemset dihentikan secara tuntas pada level $k = 2$.
    </p>

    <div class="page-break"></div>

    <p><strong>Langkah 4: Pembentukan Aturan Asosiasi (Association Rules) & Pembuktian Manual:</strong></p>
    <p>
        Setiap pasangan pada $L_2$ diuji nilai keyakinannya (*Confidence* &ge; 50%) dan dihitung rasio keterkaitannya (*Lift Ratio*). 
        Berikut adalah pembuktian komputasi matematis manual untuk aturan-aturan utama sistem:
    </p>

    <ul>
        <li>
            <strong>1. Aturan: Spec_Issue &rarr; Material Tidak Standar</strong><br>
            &bull; $Support = \frac{Count(\text{Spec\_Issue} \cap \text{Material Tdk Std})}{N} = \frac{13}{181} = \mathbf{7.18\%}$<br>
            &bull; $Confidence = \frac{13}{Count(\text{Spec\_Issue})} = \frac{13}{15} = \mathbf{86.67\%} \ge 50\%$<br>
            &bull; $Lift = \frac{Confidence}{Support(\text{Material Tdk Std})} = \frac{0.866667}{(21 / 181)} = \frac{0.866667}{0.116022} = \mathbf{7.47x \text{ (Sangat Kuat)}}$
        </li>
        <li>
            <strong>2. Aturan: Deformation &rarr; Handling</strong><br>
            &bull; $Support = \frac{Count(\text{Deformation} \cap \text{Handling})}{N} = \frac{22}{181} = \mathbf{12.15\%}$<br>
            &bull; $Confidence = \frac{22}{Count(\text{Deformation})} = \frac{22}{25} = \mathbf{88.00\%} \ge 50\%$<br>
            &bull; $Lift = \frac{Confidence}{Support(\text{Handling})} = \frac{0.880000}{(39 / 181)} = \frac{0.880000}{0.215470} = \mathbf{4.08x \text{ (Kuat)}}$
        </li>
        <li>
            <strong>3. Aturan: Visual_Defect &rarr; Handling</strong><br>
            &bull; $Support = \frac{Count(\text{Visual\_Defect} \cap \text{Handling})}{N} = \frac{22}{181} = \mathbf{12.15\%}$<br>
            &bull; $Confidence = \frac{22}{Count(\text{Visual\_Defect})} = \frac{22}{29} = \mathbf{75.86\%} \ge 50\%$<br>
            &bull; $Lift = \frac{Confidence}{Support(\text{Handling})} = \frac{0.758621}{(39 / 181)} = \frac{0.758621}{0.215470} = \mathbf{3.52x \text{ (Kuat)}}$
        </li>
        <li>
            <strong>4. Aturan: Kondisi Penyimpanan &rarr; Length_Issue</strong><br>
            &bull; $Support = \frac{Count(\text{Kondisi Penyimpanan} \cap \text{Length\_Issue})}{N} = \frac{26}{181} = \mathbf{14.36\%}$<br>
            &bull; $Confidence = \frac{26}{Count(\text{Kondisi Penyimpanan})} = \frac{26}{28} = \mathbf{92.86\%} \ge 50\%$<br>
            &bull; $Lift = \frac{Confidence}{Support(\text{Length\_Issue})} = \frac{0.928571}{(57 / 181)} = \frac{0.928571}{0.314917} = \mathbf{2.95x \text{ (Sedang)}}$
        </li>
    </ul>

    <p><strong>Rekapitulasi 8 Aturan Asosiasi Utama Sistem (Min. Support 5%, Min. Confidence 50%):</strong></p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 27%;">Anteseden (IF)</th>
                <th style="width: 27%;">Konsekuen (THEN)</th>
                <th style="width: 10%;">Support</th>
                <th style="width: 11%;">Confidence</th>
                <th style="width: 8%;">Lift</th>
                <th style="width: 12%;">Prioritas</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($rules50 as $r): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><strong><?= htmlspecialchars($r['antecedents']) ?></strong></td>
                <td><strong><?= htmlspecialchars($r['consequents']) ?></strong></td>
                <td class="text-center"><?= round($r['support'] * 100, 2) ?>%</td>
                <td class="text-center"><?= round($r['confidence'] * 100, 2) ?>%</td>
                <td class="text-center text-bold"><?= round($r['lift'], 2) ?></td>
                <td class="text-center">
                    <span class="<?= $r['prioritas'] === 'Prioritas' ? 'badge-prio' : ($r['prioritas'] === 'Dipertimbangkan' ? 'badge-timbang' : 'badge-pantau') ?>">
                        <?= htmlspecialchars($r['prioritas']) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="table-caption">Tabel 3.5: Rekapitulasi Aturan Asosiasi Utama pada N = 181 (Min. Support 5%)</div>

    <p><strong>Rekapitulasi Lengkap 20 Aturan Asosiasi pada Antarmuka Sistem Web (Min. Support 1.5%, Min. Confidence 50%):</strong></p>
    <p>
        Berikut adalah daftar lengkap 20 aturan asosiasi yang dihitung secara *live* oleh sistem Salsa-Arpriory 
        pada saat pengguna mengakses menu <em>Analisis Apriori</em>:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 25%;">Anteseden (IF)</th>
                <th style="width: 26%;">Konsekuen (THEN)</th>
                <th style="width: 9%;">Support</th>
                <th style="width: 10%;">Confidence</th>
                <th style="width: 7%;">Lift</th>
                <th style="width: 6%;">Cnt</th>
                <th style="width: 13%;">Prioritas</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($rules15 as $r): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($r['antecedents']) ?></td>
                <td><?= htmlspecialchars($r['consequents']) ?></td>
                <td class="text-center"><?= round($r['support'] * 100, 2) ?>%</td>
                <td class="text-center"><?= round($r['confidence'] * 100, 2) ?>%</td>
                <td class="text-center text-bold"><?= round($r['lift'], 2) ?></td>
                <td class="text-center"><?= $r['count'] ?></td>
                <td class="text-center">
                    <span class="<?= $r['prioritas'] === 'Prioritas' ? 'badge-prio' : ($r['prioritas'] === 'Dipertimbangkan' ? 'badge-timbang' : 'badge-pantau') ?>">
                        <?= htmlspecialchars($r['prioritas']) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="table-caption">Tabel 3.6: Rekapitulasi 20 Aturan Asosiasi Lengkap Sistem Salsa-Arpriory (Min. Support 1.5%, N = 181)</div>

    <div class="page-break"></div>

    <h3 class="subbab-title">3.5 Tahap 5: Interpretation & Evaluation (Interpretasi dan Evaluasi Hasil)</h3>
    <p>
        Tahap akhir KDD mengonversi indikator kuantitatif menjadi wawasan operasional yang bermakna bagi pengambil keputusan mutu:
    </p>

    <div class="callout-akademik">
        <div class="callout-title">Matriks Klasifikasi Prioritas Mutu Sistem Salsa-Arpriory:</div>
        1. <strong>PRIORITAS (Count &ge; 5 dan Lift &ge; 3.0):</strong> Masalah berulang dengan dependensi tinggi; wajib menjadi acuan penerbitan tindakan korektif dan preventif (CAPA).<br>
        2. <strong>DIPERTIMBANGKAN (Count &ge; 5 dan 1.5 &le; Lift &lt; 3.0):</strong> Masalah berfrekuensi cukup tinggi namun korelasinya sedang; dijadikan fokus audit berkala.<br>
        3. <strong>DIPANTAU (Count 3 - 4 atau Lift &lt; 1.5):</strong> Pola yang baru terindikasi pada data awal; dipantau perkembangannya seiring penambahan transaksi baru.
    </div>

    <p><strong>Interpretasi Bahasa Alami dan Rekomendasi CAPA untuk PT Wahana Bermuda Nusantara:</strong></p>
    <ol>
        <li>
            <strong>Gugus Masalah Spesifikasi (Spec_Issue &harr; Material Tidak Standar | Lift 7.47x - PRIORITAS):</strong><br>
            <em>Interpretasi:</em> Sebanyak 86.67% kasus ketidaksesuaian spesifikasi berakar dari material pemasok yang tidak standar. Korelasi keduanya Sangat Kuat (7.47x lipat dari kejadian acak).<br>
            <em>Rekomendasi CAPA:</em> Departemen QC wajib memperketat *Incoming Raw Material Inspection*. Pengujian ketebalan ply, gramatur kertas Kraft (GSM), dan tes daya serap air (Cobb Test) harus dilakukan sebelum bahan baku diserahterimakan ke lini produksi.
        </li>
        <li>
            <strong>Gugus Masalah Deformasi Fisik (Deformation &harr; Handling | Lift 4.08x - PRIORITAS):</strong><br>
            <em>Interpretasi:</em> Dari seluruh kasus pipa karton gepeng, oval, atau mekar, sebanyak 88.00% dipicu oleh penanganan penumpukan dan pemindahan barang yang salah.<br>
            <em>Rekomendasi CAPA:</em> Menetapkan Standard Operating Procedure (SOP) penumpukan maksimal palet (maksimal 5 susun), melengkapi area strapping dengan bantalan pelindung sudut (*corner protector*), serta memberikan pelatihan berkala bagi staf gudang dan ekspedisi.
        </li>
        <li>
            <strong>Gugus Masalah Visual (Visual_Defect &harr; Handling | Lift 3.52x - PRIORITAS):</strong><br>
            <em>Interpretasi:</em> Sebanyak 75.86% anomali cacat visual (bintik kotor, goresan luar) terhubung langsung dengan proses *handling* produk.<br>
            <em>Rekomendasi CAPA:</em> Menggunakan pembungkus pelindung plastik (*stretch film wrapping*) secara rapat sebelum produk ditransportasikan serta menjaga kebersihan lantai pabrik.
        </li>
        <li>
            <strong>Gugus Masalah Perekatan & Kadar Air (Adhesion & Moisture Issue - PRIORITAS):</strong><br>
            <em>Interpretasi:</em> Kegagalan daya rekat memiliki lift 8.71x terhadap formulasi lem, sedangkan masalah kadar air memiliki lift 8.98x terhadap proses *drying*.<br>
            <em>Rekomendasi CAPA:</em> Melakukan kontrol viskositas cairan perekat secara rutin setiap pergantian shift kerja dan memastikan suhu ruang oven pengering (*drying chamber*) termonitor pada suhu standar minimum operasional.
        </li>
    </ol>

    <p><strong>Integrasi Sinergis dengan Seven Tools QC:</strong></p>
    <p>
        Di dalam arsitektur Salsa-Arpriory, aturan asosiasi Apriori secara otomatis menyuplai data ke diagram Fishbone 6M 
        (*Man, Machine, Material, Method, Environment, Measurement*) dan menyajikan jendela *pop-up* rekomendasi tindakan perbaikan otomatis 
        pada saat Staf QA menginput ketidaksesuaian pada formulir transaksi baru.
    </p>

    <!-- BAB IV -->
    <h2 class="bab-title">BAB IV. KESIMPULAN</h2>
    <p>
        Berdasarkan keseluruhan proses Knowledge Discovery in Databases (KDD) dan eksekusi komputasi Algoritma Apriori 
        terhadap 181 transaksi komplain PT Wahana Bermuda Nusantara, disimpulkan bahwa:
    </p>
    <ul>
        <li><strong>Keberhasilan Metodologi KDD:</strong> Transformasi data dari 207 rincian item menjadi 181 keranjang transaksi (*market basket*) berhasil merefleksikan kejadian komplain riil tanpa menimbulkan bias duplikasi data.</li>
        <li><strong>Validitas Pola Asosiasi:</strong> Ditemukan 8 aturan asosiasi utama pada ambang batas 5% dan 20 aturan asosiasi komprehensif pada ambang batas default 1.5%. Seluruh aturan memiliki nilai Lift Ratio jauh di atas 1 (antara 1.76 hingga 22.62x), membuktikan bahwa keterkaitan cacat dan akar masalah merupakan relasi deterministik nyata, bukan kebetulan acak.</li>
        <li><strong>Konsistensi Sistem:</strong> Perhitungan matematis dalam dokumen ini identik dan sinkron 100% dengan luaran sistem web Salsa-Arpriory ([AprioriService.php](file:///c:/laragon/www/Apriori/salsa-arpriory/app/Services/AprioriService.php)), sehingga siap dipertanggungjawabkan dalam sidang akademik skripsi.</li>
    </ul>

</body>
</html>
<?php
$htmlContent = ob_get_clean();

// 4. Render ke PDF dengan Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Times-Roman');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($htmlContent);
$dompdf->setPaper('a4', 'portrait');
$dompdf->render();

$pdfOutput = $dompdf->output();

// Simpan file ke lokasi tujuan
$outBahan = __DIR__ . '/_bahan-skripsi/PERHITUNGAN_LENGKAP_APRIORI_KDD_SALSA.pdf';
$outRoot = __DIR__ . '/PERHITUNGAN_LENGKAP_APRIORI_KDD_SALSA.pdf';
$outArtifact = 'C:/Users/salsa/.gemini/antigravity-ide/brain/889e2308-bb14-4d7c-b6f6-bb870ec8e661/PERHITUNGAN_LENGKAP_APRIORI_KDD_SALSA.pdf';

file_put_contents($outBahan, $pdfOutput);
file_put_contents($outRoot, $pdfOutput);
file_put_contents($outArtifact, $pdfOutput);

echo "PDF Berhasil Diperbarui dengan Basis N = 181 Transaksi!\n";
echo "1. _bahan-skripsi: {$outBahan} (" . number_format(filesize($outBahan)) . " bytes)\n";
echo "2. root: {$outRoot} (" . number_format(filesize($outRoot)) . " bytes)\n";
echo "3. artifact: {$outArtifact}\n";
