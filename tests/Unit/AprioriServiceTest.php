<?php

namespace Tests\Unit;

use App\Models\ComplaintItem;
use App\Services\AprioriService;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Pengujian murni algoritma Apriori (tanpa database): item dibuat in-memory.
 */
class AprioriServiceTest extends TestCase
{
    /** @param array<int, array{0:int,1:?string,2:?string,3?:?string,4?:?string}> $rows [complaint_id, ket, penyebab, detail_ket, detail_pen] */
    private function items(array $rows): Collection
    {
        return collect($rows)->map(fn ($r) => new ComplaintItem([
            'complaint_id'           => $r[0],
            'jenis_ketidaksesuaian'  => $r[1],
            'penyebab'               => $r[2],
            'detail_ketidaksesuaian' => $r[3] ?? null,
            'detail_penyebab'        => $r[4] ?? null,
        ]));
    }

    /**
     * 10 transaksi: A+X (6x), A+Y (2x), B+Y (2x).
     * Hitungan manual: A->X support 0.6, conf 0.75, lift 1.25 ; Y->B conf 0.5, lift 2.5
     */
    private function sampleItems(): Collection
    {
        $rows = [];
        $id = 1;
        for ($i = 0; $i < 6; $i++) $rows[] = [$id++, 'A', 'X'];
        for ($i = 0; $i < 2; $i++) $rows[] = [$id++, 'A', 'Y'];
        for ($i = 0; $i < 2; $i++) $rows[] = [$id++, 'B', 'Y'];
        return $this->items($rows);
    }

    private function findRule(array $rules, string $ant, string $con): ?array
    {
        foreach ($rules as $r) {
            if ($r['antecedents'] === $ant && $r['consequents'] === $con) return $r;
        }
        return null;
    }

    public function test_transaction_count_groups_items_by_complaint(): void
    {
        $svc = (new AprioriService())->buildTransactions($this->items([
            [1, 'A', 'X'],
            [1, 'B', 'Y'],   // complaint sama => 1 basket
            [2, 'A', 'X'],
        ]));

        $this->assertSame(2, $svc->transactionCount());
    }

    public function test_invalid_values_are_ignored(): void
    {
        $svc = (new AprioriService())->buildTransactions($this->items([
            [1, '-', 'nan'],
            [2, null, ''],
            [3, 'A', 'X'],
        ]));

        $this->assertSame(1, $svc->transactionCount());
    }

    public function test_support_confidence_and_lift_are_computed_correctly(): void
    {
        $svc = (new AprioriService(0.1, 0.5))->buildTransactions($this->sampleItems());
        $rules = $svc->associationRules($svc->frequentItemsets());

        $axRule = $this->findRule($rules, 'A', 'X');
        $this->assertNotNull($axRule);
        $this->assertEqualsWithDelta(0.6, $axRule['support'], 0.0001);
        $this->assertEqualsWithDelta(0.75, $axRule['confidence'], 0.0001);
        $this->assertEqualsWithDelta(1.25, $axRule['lift'], 0.0001);
        $this->assertSame(6, $axRule['count']);
        $this->assertSame('defect_to_cause', $axRule['rule_type']);

        $ybRule = $this->findRule($rules, 'Y', 'B');
        $this->assertNotNull($ybRule);
        $this->assertEqualsWithDelta(0.5, $ybRule['confidence'], 0.0001);
        $this->assertEqualsWithDelta(2.5, $ybRule['lift'], 0.0001);
        $this->assertSame('cause_to_defect', $ybRule['rule_type']);
    }

    public function test_min_confidence_filters_weak_rules(): void
    {
        $svc = (new AprioriService(0.1, 0.9))->buildTransactions($this->sampleItems());
        $rules = $svc->associationRules($svc->frequentItemsets());

        $this->assertNull($this->findRule($rules, 'A', 'X'));   // conf 0.75 < 0.9
        $this->assertNotNull($this->findRule($rules, 'X', 'A')); // conf 1.0
        $this->assertNotNull($this->findRule($rules, 'B', 'Y')); // conf 1.0
    }

    public function test_min_support_filters_rare_itemsets(): void
    {
        // B+Y hanya 2/10 = 0.2 => hilang jika min support 0.3
        $svc = (new AprioriService(0.3, 0.1))->buildTransactions($this->sampleItems());
        $rules = $svc->associationRules($svc->frequentItemsets());

        $this->assertNull($this->findRule($rules, 'B', 'Y'));
        $this->assertNotNull($this->findRule($rules, 'A', 'X'));
    }

    public function test_rules_only_pair_defect_with_cause(): void
    {
        $svc = (new AprioriService(0.1, 0.1))->buildTransactions($this->sampleItems());
        foreach ($svc->associationRules($svc->frequentItemsets()) as $rule) {
            $this->assertNotSame('defect_to_defect', $rule['rule_type']);
            $this->assertNotSame('general', $rule['rule_type']);
        }
    }

    public function test_empty_data_returns_no_rules(): void
    {
        $svc = (new AprioriService())->buildTransactions(collect());

        $this->assertSame([], $svc->frequentItemsets());
        $this->assertSame([], $svc->associationRules());
    }

    public function test_priority_matrix(): void
    {
        $svc = new AprioriService();

        $this->assertSame('Prioritas', $svc->kategoriPrioritas(5, 3.0));
        $this->assertSame('Dipertimbangkan', $svc->kategoriPrioritas(5, 2.0));
        $this->assertSame('Dipantau', $svc->kategoriPrioritas(4, 9.0));
        $this->assertSame('Dipantau', $svc->kategoriPrioritas(5, 1.2));
    }

    /**
     * Regresi bug 5: "Bocor" tidak boleh ikut dihitung pada rincian "Bocor Halus".
     */
    public function test_detail_breakdown_uses_exact_match_not_substring(): void
    {
        $rows = [];
        $id = 1;
        for ($i = 0; $i < 3; $i++) $rows[] = [$id++, 'Bocor', 'X', 'detail-bocor', 'dp1'];
        for ($i = 0; $i < 3; $i++) $rows[] = [$id++, 'Bocor Halus', 'X', 'detail-halus', 'dp2'];

        $svc = (new AprioriService(0.1, 0.5))->buildTransactions($this->items($rows));
        $rule = $this->findRule($svc->associationRules($svc->frequentItemsets()), 'Bocor Halus', 'X');

        $this->assertNotNull($rule);
        $breakdown = $rule['detail_breakdown'];
        $this->assertSame(3, $breakdown['total_matching']);
        $this->assertCount(1, $breakdown['detail_ketidaksesuaian']);
        $this->assertSame('detail-halus', $breakdown['detail_ketidaksesuaian'][0]['detail']);
        $this->assertEquals(100.0, $breakdown['detail_ketidaksesuaian'][0]['percent']);
    }
}
