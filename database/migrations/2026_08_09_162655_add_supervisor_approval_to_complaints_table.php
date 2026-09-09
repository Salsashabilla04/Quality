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
            $table->string('supervisor_approval')->default('Approved')->after('status');
            $table->text('catatan_supervisor')->nullable()->after('supervisor_approval');
            $table->timestamp('approved_at')->nullable()->after('catatan_supervisor');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['supervisor_approval', 'catatan_supervisor', 'approved_at']);
        });
    }
};
