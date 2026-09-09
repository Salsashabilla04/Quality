<?php

use App\Http\Controllers\AprioriController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SevenToolsController;
use App\Http\Controllers\VisitController;
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

    Route::middleware('can:access-dashboard')->group(function () {
        Route::get('/seven-tools', [SevenToolsController::class, 'index'])->name('seven-tools');
        Route::get('/apriori', [AprioriController::class, 'index'])->name('apriori');
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan');
        Route::get('/visit', [VisitController::class, 'index'])->name('visit.index');
        Route::post('/visit/{complaint}/schedule', [VisitController::class, 'schedule'])->name('visit.schedule');
        Route::patch('/visit/{complaint}/done', [VisitController::class, 'markDone'])->name('visit.done');

        // Kamus Cacat QC (Knowledge Base)
        Route::get('/defect-dictionary', [\App\Http\Controllers\DefectDictionaryController::class, 'index'])->name('defect-dictionary.index');
        Route::post('/defect-dictionary', [\App\Http\Controllers\DefectDictionaryController::class, 'store'])->name('defect-dictionary.store');
        Route::put('/defect-dictionary/{defectDictionary}', [\App\Http\Controllers\DefectDictionaryController::class, 'update'])->name('defect-dictionary.update');
        Route::get('/defect-dictionary/lookup', [\App\Http\Controllers\DefectDictionaryController::class, 'apiLookup'])->name('defect-dictionary.lookup');
    });

    // Rekomendasi action (AJAX)
    Route::get('/complaints/suggest', [App\Http\Controllers\RecommendationController::class, 'suggest'])->name('complaints.suggest');

    Route::patch('/complaints/{complaint}/ajukan-validasi', [ComplaintController::class, 'ajukanValidasi'])->name('complaints.ajukan-validasi');
    Route::patch('/complaints/{complaint}/quick-close', [ComplaintController::class, 'quickClose'])->name('complaints.quick-close');
    Route::patch('/complaints/{complaint}/approve', [ComplaintController::class, 'approveNcr'])->name('complaints.approve');
    Route::resource('complaints', ComplaintController::class)->except(['show']);

    // Export & Import
    Route::post('/complaints/import', [\App\Http\Controllers\ImportController::class, 'importExcel'])->name('complaints.import');
    Route::get('/export/apriori/excel', [ExportController::class, 'aprioriExcel'])->name('export.apriori.excel');
    Route::get('/export/complaints/excel', [ExportController::class, 'complaintsExcel'])->name('export.complaints.excel');
    Route::get('/export/laporan/pdf', [ExportController::class, 'laporanPdf'])->name('export.laporan.pdf');
    Route::match(['get', 'post'], '/complaints/{complaint}/ncr-pdf', [ExportController::class, 'ncrPdf'])->name('complaints.ncr');
});
