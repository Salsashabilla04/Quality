<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            // ===== OTOMATIS =====
            $table->string('no_customer')->nullable()->index();   // running number NN-NNN (auto-generate)

            // ===== INPUT MANUAL =====
            $table->string('nama_customer');
            $table->date('tanggal_complain');
            $table->string('ukuran')->nullable();
            $table->integer('qty')->nullable();

            // Klasifikasi untuk Apriori (multi-value: satu complaint bisa >1 cacat/penyebab)
            $table->json('apriori_ketidaksesuaian')->nullable();
            $table->text('detail_ketidaksesuaian')->nullable();
            $table->json('apriori_penyebab')->nullable();
            $table->text('detail_penyebab')->nullable();

            // Rincian klasifikasi (baris berulang): [{ket, det_ket, pen, det_pen}, ...]
            $table->json('items')->nullable();

            // Fishbone 6M per-complaint: { "Man":[...], "Machine":[...], ... }
            $table->json('fishbone')->nullable();

            // Tindakan
            $table->text('corrective_action')->nullable();
            $table->text('preventive_action')->nullable();

            // Tanggal & lokasi
            $table->date('tanggal_kirim')->nullable();
            $table->date('tanggal_produksi')->nullable();
            $table->string('area')->nullable();

            // Keterangan & status
            $table->string('keterangan')->nullable();             // Retur / Feedback / -
            $table->string('status')->default('Open');            // Open / Close (auto-default)

            // ===== OTOMATIS (derived) =====
            $table->integer('lead_time')->nullable();             // hari: tanggal_complain - tanggal_produksi

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
