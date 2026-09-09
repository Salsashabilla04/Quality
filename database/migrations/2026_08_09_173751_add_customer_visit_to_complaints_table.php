<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->boolean('perlu_visit')->default(false)->after('catatan_supervisor');
            $table->date('tanggal_visit')->nullable()->after('perlu_visit');
            $table->text('catatan_visit')->nullable()->after('tanggal_visit');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['perlu_visit', 'tanggal_visit', 'catatan_visit']);
        });
    }
};
