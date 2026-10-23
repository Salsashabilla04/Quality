<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$data = [
    'paretoJenis' => ['labels'=>['A'],'values'=>[1],'cumulative'=>[100]],
    'paretoDetail' => ['labels'=>['A'],'values'=>[1],'cumulative'=>[100]],
    'paretoCust' => ['labels'=>['A'],'values'=>[1],'cumulative'=>[100]],
    'paretoCause' => ['labels'=>['A'],'values'=>[1],'cumulative'=>[100]],
    'topRules' => [],
    'totalTrx' => 0,
    'tahunLaporan' => 2026,
    'totalComplaint' => 1,
    'totalOpen' => 0,
    'totalDiproses' => 0,
    'totalClose' => 1,
    'totalQtyTahun' => 10,
    'trendLabels' => ['Jan'],
    'trendData' => [1],
    'topCustomer' => collect(),
    'kpi' => [
        'total_complaint' => 1,
        'close' => 1,
        'persen_close' => 100,
        'avg_lead_time' => 1,
        'top_defect_name' => 'A',
        'top_defect_count' => 1,
        'top_cause_name' => 'A',
        'top_cause_count' => 1,
        'jumlah_customer' => 1,
        'total_qty' => 1,
    ],
    'ncrApproved' => 0,
    'ncrPending' => 0,
    'ncrRejected' => 0,
    'pendingNcrList' => collect(),
    'upcomingVisits' => collect(),
    'visitActiveTotal' => 0
];
file_put_contents('scratch/test_render.html', view('dashboard-spv', $data)->render());
echo "Rendered to scratch/test_render.html\n";
