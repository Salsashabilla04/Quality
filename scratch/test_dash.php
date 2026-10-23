<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$spv = App\Models\User::where('role', 'supervisor')->first();
auth()->login($spv);
$resSpv = app(App\Http\Controllers\DashboardController::class)->index(request());
echo "SPV View: " . $resSpv->name() . " (HTML bytes: " . strlen($resSpv->render()) . ")\n";

$qa = App\Models\User::where('role', 'qa')->first();
auth()->login($qa);
$resQa = app(App\Http\Controllers\DashboardController::class)->index(request());
echo "QA View: " . $resQa->name() . " (HTML bytes: " . strlen($resQa->render()) . ")\n";
