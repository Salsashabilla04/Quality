<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to drop unused columns.
     */
    public function up(): void
    {
        // 1. Drop foto_bukti from complaints
        if (Schema::hasColumn('complaints', 'foto_bukti')) {
            Schema::table('complaints', function (Blueprint $table) {
                $table->dropColumn('foto_bukti');
            });
        }

        // 2. Drop detail_standar & penyebab_terkait from defect_dictionaries
        Schema::table('defect_dictionaries', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('defect_dictionaries', 'detail_standar')) {
                $columnsToDrop[] = 'detail_standar';
            }
            if (Schema::hasColumn('defect_dictionaries', 'penyebab_terkait')) {
                $columnsToDrop[] = 'penyebab_terkait';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->json('foto_bukti')->nullable()->after('area');
        });

        Schema::table('defect_dictionaries', function (Blueprint $table) {
            $table->json('detail_standar')->nullable()->after('definisi');
            $table->json('penyebab_terkait')->nullable()->after('detail_standar');
        });
    }
};
