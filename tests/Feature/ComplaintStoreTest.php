<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\ComplaintItem;
use App\Models\User;
use App\Services\AprioriService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintStoreTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name'     => ucfirst($role) . ' Test',
            'email'    => "{$role}@test.local",
            'role'     => $role,
            'password' => 'password',
        ]);
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'nama_customer'    => 'PT Test',
            'tanggal_complain' => now()->toDateString(),
            'complaint_items'  => [
                ['ket_val' => 'Bocor', 'ket_det' => 'bocor di sambungan', 'pen_val' => 'Operator', 'pen_det' => 'salah setting'],
            ],
        ], $override);
    }

    public function test_qa_can_create_complaint_and_items_are_saved(): void
    {
        $response = $this->actingAs($this->user('qa'))->post('/complaints', $this->payload());

        $response->assertRedirect(route('complaints.index'));
        $this->assertSame(1, Complaint::count());
        $this->assertSame(1, ComplaintItem::count());
        $this->assertSame('Open', Complaint::first()->status);
    }

    /** Regresi bug 4: klien tidak boleh membuat complaint langsung berstatus Close. */
    public function test_client_cannot_force_status_close_on_store(): void
    {
        $this->actingAs($this->user('qa'))
            ->post('/complaints', $this->payload(['status' => 'Close']));

        $this->assertSame('Open', Complaint::firstOrFail()->status);
    }

    public function test_supervisor_cannot_submit_complaint(): void
    {
        $this->actingAs($this->user('supervisor'))
            ->post('/complaints', $this->payload())
            ->assertForbidden();

        $this->assertSame(0, Complaint::count());
    }

    public function test_guest_cannot_submit_complaint(): void
    {
        $this->post('/complaints', $this->payload())->assertRedirect('/login');
        $this->assertSame(0, Complaint::count());
    }

    public function test_customer_name_is_required(): void
    {
        $this->actingAs($this->user('qa'))
            ->post('/complaints', $this->payload(['nama_customer' => '']))
            ->assertSessionHasErrors('nama_customer');
    }

    /** Regresi bug 1: clearCache() harus membatalkan hasil cache untuk SEMUA parameter. */
    public function test_clear_cache_invalidates_all_parameter_variants(): void
    {
        $qa = $this->user('qa');
        $this->seedPair(1);

        $a1 = AprioriService::getCachedResult(0.1, 0.5)['totalTrx'];
        $b1 = AprioriService::getCachedResult(0.2, 0.6)['totalTrx'];
        $this->assertSame(1, $a1);
        $this->assertSame(1, $b1);

        // data bertambah lewat store() => cache otomatis dibersihkan
        $this->actingAs($qa)->post('/complaints', $this->payload());

        $this->assertSame(2, AprioriService::getCachedResult(0.1, 0.5)['totalTrx']);
        $this->assertSame(2, AprioriService::getCachedResult(0.2, 0.6)['totalTrx']);
    }

    public function test_cache_is_reused_until_cleared(): void
    {
        $this->seedPair(1);
        $this->assertSame(1, AprioriService::getCachedResult()['totalTrx']);

        $this->seedPair(2); // tanpa clearCache
        $this->assertSame(1, AprioriService::getCachedResult()['totalTrx']);

        AprioriService::clearCache();
        $this->assertSame(2, AprioriService::getCachedResult()['totalTrx']);
    }

    private function seedPair(int $n): void
    {
        $c = Complaint::create([
            'no_customer'      => sprintf('%02d-%03d', $n, $n),
            'nama_customer'    => "Cust {$n}",
            'tanggal_complain' => now()->toDateString(),
            'status'           => 'Open',
        ]);
        $c->items()->create(['jenis_ketidaksesuaian' => 'Bocor', 'penyebab' => 'Operator']);
    }
}
