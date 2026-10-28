<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Regresi bug 2 (cache Apriori dibersihkan di semua aksi yang mengubah complaint)
 * dan bug 3 (validasi tanggal_visit tidak memblokir tanggal lama yang tidak berubah).
 */
class ComplaintWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role), 'email' => "{$role}@test.local", 'role' => $role, 'password' => 'password',
        ]);
    }

    private function complaint(array $attrs = []): Complaint
    {
        $c = Complaint::create(array_merge([
            'no_customer'      => '01-001',
            'nama_customer'    => 'PT Test',
            'tanggal_complain' => now()->toDateString(),
            'status'           => 'Open',
        ], $attrs));
        $c->items()->create(['jenis_ketidaksesuaian' => 'Bocor', 'penyebab' => 'Operator']);

        return $c;
    }

    private function cacheVersion(): int
    {
        return (int) Cache::get('apriori_cache_version', 1);
    }

    private function updatePayload(array $override = []): array
    {
        return array_merge([
            'nama_customer'    => 'PT Test',
            'tanggal_complain' => now()->toDateString(),
            'complaint_items'  => [
                ['ket_val' => 'Bocor', 'pen_val' => 'Operator'],
            ],
        ], $override);
    }

    // ---------- Bug 2: cache dibersihkan ----------

    public function test_quick_close_clears_apriori_cache(): void
    {
        $c = $this->complaint();
        $before = $this->cacheVersion();

        $this->actingAs($this->user('qa'))->patch("/complaints/{$c->id}/quick-close");

        $this->assertSame('Close', $c->fresh()->status);
        $this->assertGreaterThan($before, $this->cacheVersion());
    }

    public function test_ajukan_validasi_clears_apriori_cache(): void
    {
        $c = $this->complaint();
        $before = $this->cacheVersion();

        $this->actingAs($this->user('qa'))->patch("/complaints/{$c->id}/ajukan-validasi");

        $this->assertSame('Diproses', $c->fresh()->status);
        $this->assertGreaterThan($before, $this->cacheVersion());
    }

    public function test_approve_ncr_clears_apriori_cache(): void
    {
        $c = $this->complaint(['status' => 'Diproses', 'supervisor_approval' => 'Pending']);
        $before = $this->cacheVersion();

        $this->actingAs($this->user('supervisor'))
            ->patch("/complaints/{$c->id}/approve", ['approval_status' => 'Approved']);

        $this->assertSame('Close', $c->fresh()->status);
        $this->assertGreaterThan($before, $this->cacheVersion());
    }

    // ---------- Bug 3: validasi tanggal_visit ----------

    public function test_update_keeps_old_unchanged_visit_date_valid(): void
    {
        $old = now()->subDays(20)->toDateString();
        $c = $this->complaint(['tanggal_visit' => $old, 'perlu_visit' => true]);

        $this->actingAs($this->user('qa'))
            ->put("/complaints/{$c->id}", $this->updatePayload([
                'tanggal_visit' => $old,
                'deskripsi_customer' => 'diedit',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame('diedit', $c->fresh()->deskripsi_customer);
    }

    public function test_update_rejects_changing_visit_date_into_the_past(): void
    {
        $c = $this->complaint(['tanggal_visit' => now()->addDays(5)->toDateString(), 'perlu_visit' => true]);

        $this->actingAs($this->user('qa'))
            ->put("/complaints/{$c->id}", $this->updatePayload([
                'tanggal_visit' => now()->subDays(3)->toDateString(),
            ]))
            ->assertSessionHasErrors('tanggal_visit');
    }

    public function test_store_rejects_past_visit_date(): void
    {
        $this->actingAs($this->user('qa'))
            ->post('/complaints', $this->updatePayload([
                'tanggal_visit' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('tanggal_visit');

        $this->assertSame(0, Complaint::count());
    }

    public function test_approve_ncr_accepts_unchanged_old_visit_date(): void
    {
        $old = now()->subDays(10)->toDateString();
        $c = $this->complaint([
            'status' => 'Diproses', 'supervisor_approval' => 'Pending',
            'tanggal_visit' => $old, 'perlu_visit' => true,
        ]);

        $this->actingAs($this->user('supervisor'))
            ->patch("/complaints/{$c->id}/approve", [
                'approval_status' => 'Approved',
                'perlu_visit'     => 1,
                'tanggal_visit'   => $old,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Close', $c->fresh()->status);
    }
}
