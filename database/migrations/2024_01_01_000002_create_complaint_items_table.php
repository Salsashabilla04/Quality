<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the new complaint_items table
        Schema::create('complaint_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_ketidaksesuaian')->nullable();
            $table->text('detail_ketidaksesuaian')->nullable();
            $table->string('penyebab')->nullable();
            $table->text('detail_penyebab')->nullable();
            $table->timestamps();
        });

        // 2. Migrate existing data from JSON columns into complaint_items
        $complaints = DB::table('complaints')->get();

        foreach ($complaints as $c) {
            $items  = json_decode($c->items ?? 'null', true);
            $ketArr = json_decode($c->apriori_ketidaksesuaian ?? 'null', true);
            $penArr = json_decode($c->apriori_penyebab ?? 'null', true);

            // Strategy A: use structured "items" column (ket/pen pairs) if available
            if (is_array($items) && !empty($items['ket'])) {
                $ketItems = array_values(array_filter($items['ket'] ?? [], fn ($r) => ($r['v'] ?? '') !== '' || ($r['d'] ?? '') !== ''));
                $penItems = array_values(array_filter($items['pen'] ?? [], fn ($r) => ($r['v'] ?? '') !== '' || ($r['d'] ?? '') !== ''));

                $maxRows = max(count($ketItems), count($penItems), 1);
                for ($i = 0; $i < $maxRows; $i++) {
                    DB::table('complaint_items')->insert([
                        'complaint_id'           => $c->id,
                        'jenis_ketidaksesuaian'  => $ketItems[$i]['v'] ?? null,
                        'detail_ketidaksesuaian' => $ketItems[$i]['d'] ?? null,
                        'penyebab'               => $penItems[$i]['v'] ?? null,
                        'detail_penyebab'        => $penItems[$i]['d'] ?? null,
                        'created_at'             => $c->created_at,
                        'updated_at'             => $c->updated_at,
                    ]);
                }
                continue;
            }

            // Strategy B: fall back to the flat JSON arrays + detail text
            $ketList = is_array($ketArr) ? array_values(array_filter(array_map('trim', $ketArr))) : [];
            $penList = is_array($penArr) ? array_values(array_filter(array_map('trim', $penArr))) : [];

            if (empty($ketList) && empty($penList)) {
                continue; // nothing to migrate
            }

            $maxRows = max(count($ketList), count($penList), 1);
            for ($i = 0; $i < $maxRows; $i++) {
                DB::table('complaint_items')->insert([
                    'complaint_id'           => $c->id,
                    'jenis_ketidaksesuaian'  => $ketList[$i] ?? null,
                    'detail_ketidaksesuaian' => $i === 0 ? $c->detail_ketidaksesuaian : null,
                    'penyebab'               => $penList[$i] ?? null,
                    'detail_penyebab'        => $i === 0 ? $c->detail_penyebab : null,
                    'created_at'             => $c->created_at,
                    'updated_at'             => $c->updated_at,
                ]);
            }
        }

        // 3. Drop the old columns from complaints
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn([
                'apriori_ketidaksesuaian',
                'detail_ketidaksesuaian',
                'apriori_penyebab',
                'detail_penyebab',
                'items',
            ]);
        });
    }

    public function down(): void
    {
        // Restore old columns
        Schema::table('complaints', function (Blueprint $table) {
            $table->json('apriori_ketidaksesuaian')->nullable()->after('qty');
            $table->text('detail_ketidaksesuaian')->nullable()->after('apriori_ketidaksesuaian');
            $table->json('apriori_penyebab')->nullable()->after('detail_ketidaksesuaian');
            $table->text('detail_penyebab')->nullable()->after('apriori_penyebab');
            $table->json('items')->nullable()->after('detail_penyebab');
        });

        // Migrate data back (best-effort)
        foreach (DB::table('complaint_items')->get()->groupBy('complaint_id') as $complaintId => $rows) {
            $ketArr = $rows->pluck('jenis_ketidaksesuaian')->filter()->unique()->values()->all();
            $penArr = $rows->pluck('penyebab')->filter()->unique()->values()->all();
            $detKet = $rows->pluck('detail_ketidaksesuaian')->filter()->first();
            $detPen = $rows->pluck('detail_penyebab')->filter()->first();

            DB::table('complaints')->where('id', $complaintId)->update([
                'apriori_ketidaksesuaian' => json_encode($ketArr),
                'detail_ketidaksesuaian'  => $detKet,
                'apriori_penyebab'        => json_encode($penArr),
                'detail_penyebab'         => $detPen,
                'items'                   => json_encode([
                    'ket' => $rows->map(fn ($r) => ['v' => $r->jenis_ketidaksesuaian ?? '', 'd' => $r->detail_ketidaksesuaian ?? ''])->values()->all(),
                    'pen' => $rows->map(fn ($r) => ['v' => $r->penyebab ?? '', 'd' => $r->detail_penyebab ?? ''])->values()->all(),
                ]),
            ]);
        }

        Schema::dropIfExists('complaint_items');
    }
};
