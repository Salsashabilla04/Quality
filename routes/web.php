<?php

use App\Http\Controllers\AprioriController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SevenToolsController;
use Illuminate\Support\Facades\Route;

// ===== Guest (login) =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// ===== Admin (butuh login) =====
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/seven-tools', [SevenToolsController::class, 'index'])->name('seven-tools');
    Route::get('/apriori', [AprioriController::class, 'index'])->name('apriori');

    // Rekomendasi action (AJAX)
    Route::get('/complaints/suggest', [RecommendationController::class, 'suggest'])->name('complaints.suggest');

    Route::resource('complaints', ComplaintController::class)->except(['show']);

    // Export & Import
    Route::post('/complaints/import', [\App\Http\Controllers\ImportController::class, 'importExcel'])->name('complaints.import');
    Route::get('/export/apriori/excel', [ExportController::class, 'aprioriExcel'])->name('export.apriori.excel');
    Route::get('/export/complaints/excel', [ExportController::class, 'complaintsExcel'])->name('export.complaints.excel');
    Route::get('/export/laporan/pdf', [ExportController::class, 'laporanPdf'])->name('export.laporan.pdf');
    Route::match(['get', 'post'], '/complaints/{complaint}/ncr-pdf', [ExportController::class, 'ncrPdf'])->name('complaints.ncr');
});
