<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Validasi server-side parameter halaman Apriori dan hasil default pada data asli (seed_complaints.json).
 */
class AprioriParameterTest extends TestCase
{
    use RefreshDatabase;

    private function login(): void
    {
        $this->actingAs(User::create([
            'name' => 'QA Test', 'email' => 'qa@test.local', 'role' => 'qa', 'password' => 'password',
        ]));
    }

    /** Muat data asli seperti ComplaintSeeder: 1 complaint per no_customer, item per baris. */
    private function seedRealData(): void
    {
        $rows = json_decode(file_get_contents(database_path('data/seed_complaints.json')), true);
        $groups = [];
        foreach ($rows as $r) {
            if (! empty($r['no_customer'])) $groups[$r['no_customer']][] = $r;
        }
        foreach ($groups as $no => $group) {
            if (empty($group[0]['tanggal_complain'])) continue;
            $c = Complaint::create([
                'no_customer'      => $no,
                'nama_customer'    => $group[0]['nama_customer'] ?? 'Tidak diketahui',
                'tanggal_complain' => $group[0]['tanggal_complain'],
                'status'           => 'Open',
            ]);
            foreach ($group as $r) {
                $ket = trim((string) ($r['apriori_ketidaksesuaian'] ?? ''));
                $pen = trim((string) ($r['apriori_penyebab'] ?? ''));
                if ($ket === '' && $pen === '') continue;
                $c->items()->create([
                    'jenis_ketidaksesuaian' => $ket ?: null,
                    'penyebab'              => $pen ?: null,
                ]);
            }
        }
    }

    public function test_support_zero_is_rejected(): void
    {
        $this->login();
        $this->get('/apriori?min_support=0')->assertSessionHasErrors('min_support');
    }

    public function test_support_above_100_is_rejected(): void
    {
        $this->login();
        $this->get('/apriori?min_support=150')->assertSessionHasErrors('min_support');
    }

    public function test_support_non_numeric_is_rejected(): void
    {
        $this->login();
        $this->get('/apriori?min_support=abc')->assertSessionHasErrors('min_support');
    }

    public function test_confidence_out_of_range_is_rejected(): void
    {
        $this->login();
        $this->get('/apriori?min_confidence=5')->assertSessionHasErrors('min_confidence');
        $this->get('/apriori?min_confidence=101')->assertSessionHasErrors('min_confidence');
    }

    public function test_confidence_10_is_accepted(): void
    {
        $this->login();
        $this->get('/apriori?min_confidence=10')->assertOk()->assertSessionHasNoErrors();
    }

    public function test_month_13_is_rejected_and_zero_means_all(): void
    {
        $this->login();
        $this->get('/apriori?bulan=13')->assertSessionHasErrors('bulan');
        $this->get('/apriori?bulan=0&tahun=0')->assertOk()->assertSessionHasNoErrors();
    }

    public function test_error_message_is_indonesian(): void
    {
        $this->login();
        $this->get('/apriori?min_support=0')->assertSessionHasErrors([
            'min_support' => 'Minimum support harus berupa angka antara 0,5% dan 100%.',
        ]);
    }

    public function test_decimal_values_are_accepted(): void
    {
        $this->login();
        $this->get('/apriori?min_support=2.5&min_confidence=60')->assertOk()->assertSessionHasNoErrors();
    }

    public function test_empty_parameters_use_defaults_and_yield_20_rules_on_real_data(): void
    {
        $this->login();
        $this->seedRealData();

        $response = $this->get('/apriori?min_support=&min_confidence=')
            ->assertOk()
            ->assertSessionHasNoErrors();

        $this->assertSame(1.5, $response->viewData('minSupport'));
        $this->assertSame(50.0, $response->viewData('minConfidence'));
        $this->assertSame(181, $response->viewData('totalTrx'));
        $this->assertCount(20, $response->viewData('rules'));
    }
}
