<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AprioriService;

$minS = 0.015; $minC = 0.5;
$svc = (new AprioriService($minS, $minC))->buildTransactions();
$freq = $svc->frequentItemsets();
$rules = $svc->associationRules($freq);
$n = $svc->transactionCount();
$pct = fn($v) => number_format($v * 100, 1) . '%';
$minCount = ceil($minS * $n);

$o = "# Perhitungan Manual Algoritma Apriori\n\n";
$o .= "Proses pencarian pola asosiasi (Apriori) pada dataset cacat produk. Setiap aturan berbentuk **A → B** dengan satu Ketidaksesuaian dan satu Penyebab.\n";
$o .= "- **Total Transaksi (N):** $n\n- **Minimum Support:** " . ($minS * 100) . "% (minimal $minCount transaksi)\n- **Minimum Confidence:** " . ($minC * 100) . "%\n\n";

$o .= "## Kriteria Pengambilan Keputusan Tindak Lanjut\n\n";
$o .= "| Kategori Prioritas | Syarat Kriteria | Rekomendasi Tindak Lanjut Mutu |\n";
$o .= "|---|---|---|\n";
$o .= "| **Prioritas** | Jumlah kejadian $\ge 5$ dan Lift $\ge 3$ | Dijadikan dasar utama tindakan korektif dan pencegahan |\n";
$o .= "| **Dipertimbangkan** | Jumlah kejadian $\ge 5$ dan $1,5 \le \\text{Lift} < 3$ | Dipertimbangkan sebagai faktor pendukung |\n";
$o .= "| **Dipantau** | Jumlah kejadian 3 sampai 4 | Dipantau seiring bertambahnya data |\n\n";

foreach ([1 => 'Pembentukan Itemset 1 (C1 & L1)', 2 => 'Pembentukan Itemset 2 (C2 & L2)'] as $size => $title) {
    $o .= "## $title\n\n| Itemset | Jumlah (Count) | Support | Keterangan |\n|---|---|---|---|\n";
    $rows = array_filter($freq, fn($f) => $f['size'] === $size);
    usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
    foreach ($rows as $f) {
        $o .= '| ' . implode(', ', $f['items']) . " | {$f['count']} | " . $pct($f['support']) . " | Memenuhi |\n";
    }
    $o .= "\n";
}

$o .= "## Pembentukan Aturan Asosiasi (Association Rules)\n\nAturan dengan Confidence >= " . ($minC * 100) . "%, diklasifikasikan dan diurutkan berdasarkan Kategori Prioritas lalu Lift Ratio.\n\n";
$o .= "| Aturan (Rule) | Jumlah Kejadian | Support | Confidence | Lift Ratio | Kategori Prioritas | Rekomendasi Tindak Lanjut |\n|---|---|---|---|---|---|---|\n";
foreach ($rules as $r) {
    $o .= "| Jika {$r['antecedents']} maka {$r['consequents']} | {$r['count']} | " . $pct($r['support']) . ' | ' . $pct($r['confidence']) . ' | ' . number_format($r['lift'], 2) . " | **{$r['prioritas']}** | {$r['tindak_lanjut']} |\n";
}
$o .= "\n## Kesimpulan (Interpretasi & Prioritas Tindak Lanjut)\n";
foreach ($rules as $i => $r) {
    if ($r['prioritas'] === 'Prioritas') {
        $o .= "- **Prioritas #" . ($i + 1) . "**: {$r['interpretasi']} Tindak lanjut: *{$r['tindak_lanjut']}*.\n";
    }
}
file_put_contents(__DIR__ . '/../perhitungan_apriori_bab3.md', $o);
echo "OK n=$n freq=" . count($freq) . " rules=" . count($rules) . "\n";
