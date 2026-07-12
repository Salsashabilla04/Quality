<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Rekomendasi corrective & preventive action berdasarkan:
 *  1) histori data complaint (action yang paling sering dipakai untuk kasus serupa), dan
 *  2) association rules Apriori (insight kaitan ketidaksesuaian ↔ penyebab).
 *
 * Mendukung multi-pilih: ketidaksesuaian & penyebab bisa berisi lebih dari satu kategori.
 */
class RecommendationService
{
    public function suggest($ketidaksesuaian, $penyebab): array
    {
        $ketArr = Complaint::normalizeCategories($ketidaksesuaian, $this->existing('jenis_ketidaksesuaian'));
        $penArr = Complaint::normalizeCategories($penyebab, $this->existing('penyebab'));

        $all = Complaint::with('items')->get();

        return [
            'corrective' => $this->topActions($all, 'corrective_action', $ketArr, $penArr),
            'preventive' => $this->topActions($all, 'preventive_action', $ketArr, $penArr),
            'rules'      => $this->matchingRules(array_merge($ketArr, $penArr)),
            'note'       => (empty($ketArr) && empty($penArr))
                ? 'Pilih jenis ketidaksesuaian dan/atau penyebab untuk melihat rekomendasi.'
                : 'Rekomendasi diambil dari tindakan yang paling sering dipakai pada kasus serupa serta kaitan Apriori.',
        ];
    }

    /**
     * Action terpopuler berdasarkan layer prioritas:
     *  (a) kombinasi  → complaint mengandung salah satu ketidaksesuaian DAN salah satu penyebab terpilih
     *  (b) penyebab   → mengandung salah satu penyebab terpilih
     *  (c) jenis      → mengandung salah satu ketidaksesuaian terpilih
     * Digabung tanpa duplikat, maksimal 4 saran.
     */
    protected function topActions(Collection $all, string $field, array $ketArr, array $penArr): array
    {
        $layers = [];
        if ($ketArr && $penArr) {
            $layers[] = ['basis' => 'kombinasi', 'fn' => fn ($c) =>
                $this->itemsContain($c, 'jenis_ketidaksesuaian', $ketArr) && $this->itemsContain($c, 'penyebab', $penArr)];
        }
        if ($penArr) {
            $layers[] = ['basis' => 'penyebab', 'fn' => fn ($c) => $this->itemsContain($c, 'penyebab', $penArr)];
        }
        if ($ketArr) {
            $layers[] = ['basis' => 'jenis', 'fn' => fn ($c) => $this->itemsContain($c, 'jenis_ketidaksesuaian', $ketArr)];
        }

        $result = [];
        $seen = [];
        foreach ($layers as $layer) {
            $counts = [];
            foreach ($all->filter($layer['fn']) as $c) {
                $val = trim((string) $c->{$field});
                if ($val === '' || $val === '-') continue;
                $counts[$val] = ($counts[$val] ?? 0) + 1;
            }
            arsort($counts);

            foreach ($counts as $val => $cnt) {
                $norm = Str::lower($val);
                if (isset($seen[$norm])) continue;
                $seen[$norm] = true;
                $result[] = ['value' => $val, 'count' => $cnt, 'basis' => $layer['basis']];
                if (count($result) >= 4) return $result;
            }
        }
        return $result;
    }

    /** Cek apakah items complaint mengandung salah satu nilai terpilih. */
    protected function itemsContain(Complaint $c, string $itemField, array $selected): bool
    {
        foreach ($c->items as $item) {
            $val = trim((string) $item->{$itemField});
            if (in_array($val, $selected, true)) return true;
        }
        return false;
    }

    /** Association rules yang melibatkan salah satu kategori terpilih. */
    protected function matchingRules(array $targets): array
    {
        $targets = array_values(array_filter(array_unique($targets)));
        if (empty($targets)) return [];

        $rules = (new AprioriService(0.05, 0.5))->buildTransactions()->associationRules();

        $matched = [];
        foreach ($rules as $rule) {
            $hay = $rule['antecedents'] . ' ' . $rule['consequents'];
            foreach ($targets as $t) {
                if (Str::contains($hay, $t)) { $matched[] = $rule; break; }
            }
        }
        return array_slice($matched, 0, 5);
    }

    /** Daftar kategori yang sudah ada (bawaan + dari DB) untuk normalisasi. */
    protected function existing(string $column): array
    {
        $defaults = $column === 'penyebab' ? Complaint::PENYEBAB : Complaint::KETIDAKSESUAIAN;
        $fromDb = ComplaintItem::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->pluck($column)
            ->all();
        return array_values(array_unique(array_merge($defaults, $fromDb)));
    }
}
