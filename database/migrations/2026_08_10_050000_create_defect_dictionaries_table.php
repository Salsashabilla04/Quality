<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defect_dictionaries', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_ketidaksesuaian')->unique();
            $table->text('definisi')->nullable();
            $table->json('detail_standar')->nullable();
            $table->json('penyebab_terkait')->nullable();
            $table->text('tindakan_rekomendasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defect_dictionaries');
    }
};
