<?php

namespace App\Services;

use App\Models\ComplaintItem;
use Illuminate\Support\Collection;

/**
 * Implementasi Algoritma Apriori murni di PHP (mengikuti logika apriori_complain.py).
 *
 * Item dibentuk dari kolom "jenis_ketidaksesuaian" dan "penyebab" di tabel complaint_items.
 * Menghasilkan frequent itemsets (berdasarkan min support) dan
 * association rules (berdasarkan min confidence) lengkap dengan lift,
 * kekuatan kaitan, dan interpretasi bahasa Indonesia.
 */
class AprioriService
{
    protected float $minSupport;
    protected float $minConfidence;

    /** @var array<int, array<int, string>> daftar transaksi (tiap transaksi = list item) */
    protected array $transactions = [];

    public function __construct(float $minSupport = 0.05, float $minConfidence = 0.5)
    {
        $this->minSupport = $minSupport;
        $this->minConfidence = $minConfidence;
    }

    /**
     * Bentuk transaksi dari tabel complaint_items, di-groupBy complaint_id.
     * Tiap group (complaint_id) = 1 transaksi/basket berisi semua cacat & penyebab.
     *
     * @param Collection|null $items jika null, query dari DB langsung
     */
    public function buildTransactions(?Collection $items = null): self
    {
        $items = $items ?? ComplaintItem::all();
        $grouped = $items->groupBy('complaint_id');

        $this->transactions = [];
        foreach ($grouped as $complaintId => $group) {
            $basket = [];
            foreach ($group as $item) {
                $ket = is_array($item) ? ($item['jenis_ketidaksesuaian'] ?? null) : $item->jenis_ketidaksesuaian;
                $pen = is_array($item) ? ($item['penyebab'] ?? null) : $item->penyebab;

                if ($this->valid($ket)) $basket[] = 'Ketidaksesuaian=' . trim($ket);
                if ($this->valid($pen)) $basket[] = 'Penyebab=' . trim($pen);
            }
            if (! empty($basket)) {
                $this->transactions[] = array_values(array_unique($basket));
            }
        }
        return $this;
    }

    protected function valid($v): bool
    {
        if ($v === null) return false;
        $v = strtolower(trim((string) $v));
        return ! in_array($v, ['', '-', 'nan', 'null'], true);
    }

    public function transactionCount(): int
    {
        return count($this->transactions);
    }

    /**
     * Hitung frequent itemsets level demi level (Apriori).
     *
     * @return array<int, array{items: string[], support: float, count: int, size: int}>
     */
    public function frequentItemsets(): array
    {
        $n = count($this->transactions);
        if ($n === 0) return [];

        $minCount = $this->minSupport * $n;
        $allFrequent = [];

        // ---- Level 1 ----
        $count1 = [];
        foreach ($this->transactions as $t) {
            foreach ($t as $item) {
                $count1[$item] = ($count1[$item] ?? 0) + 1;
            }
        }

        $current = []; // frequent k-itemsets sebagai array string item (sorted)
        foreach ($count1 as $item => $cnt) {
            if ($cnt >= $minCount) {
                $key = $item;
                $current[$key] = [$item];
                $allFrequent[] = [
                    'items'   => [$item],
                    'count'   => $cnt,
                    'support' => $cnt / $n,
                    'size'    => 1,
                ];
            }
        }

        // ---- Level k >= 2 ----
        $k = 2;
        while (! empty($current)) {
            $candidates = $this->generateCandidates(array_values($current), $k);
            if (empty($candidates)) break;

            // hitung support tiap candidate
            $countK = [];
            foreach ($candidates as $key => $cand) {
                $countK[$key] = 0;
            }
            foreach ($this->transactions as $t) {
                $tset = array_flip($t);
                foreach ($candidates as $key => $cand) {
                    $contains = true;
                    foreach ($cand as $item) {
                        if (! isset($tset[$item])) { $contains = false; break; }
                    }
                    if ($contains) $countK[$key]++;
                }
            }

            $next = [];
            foreach ($candidates as $key => $cand) {
                if ($countK[$key] >= $minCount) {
                    $next[$key] = $cand;
                    $allFrequent[] = [
                        'items'   => $cand,
                        'count'   => $countK[$key],
                        'support' => $countK[$key] / $n,
                        'size'    => $k,
                    ];
                }
            }

            $current = $next;
            $k++;
        }

        // urutkan support desc
        usort($allFrequent, fn ($a, $b) => $b['support'] <=> $a['support']);
        return $allFrequent;
    }

    /**
     * Gabungkan frequent (k-1)-itemsets menjadi candidate k-itemsets.
     */
    protected function generateCandidates(array $frequentPrev, int $k): array
    {
        $candidates = [];
        $m = count($frequentPrev);
        for ($i = 0; $i < $m; $i++) {
            for ($j = $i + 1; $j < $m; $j++) {
                $union = array_values(array_unique(array_merge($frequentPrev[$i], $frequentPrev[$j])));
                if (count($union) === $k) {
                    sort($union);
                    $key = implode('||', $union);
                    $candidates[$key] = $union;
                }
            }
        }
        return $candidates;
    }

    /**
     * Bentuk association rules dari frequent itemsets (size >= 2).
     *
     * @return array<int, array<string, mixed>>
     */
    public function associationRules(?array $frequent = null): array
    {
        $frequent = $frequent ?? $this->frequentItemsets();
        $n = count($this->transactions);
        if ($n === 0) return [];

        // index support per itemset-key untuk lookup
        $supportMap = [];
        foreach ($frequent as $f) {
            $key = $this->key($f['items']);
            $supportMap[$key] = $f['support'];
        }

        $rules = [];
        foreach ($frequent as $f) {
            if ($f['size'] < 2) continue;
            $items = $f['items'];
            $supportAll = $f['support'];

            // semua subset non-kosong sebagai antecedent
            foreach ($this->properSubsets($items) as $antecedent) {
                $consequent = array_values(array_diff($items, $antecedent));
                if (empty($consequent)) continue;

                $supA = $supportMap[$this->key($antecedent)] ?? null;
                $supC = $supportMap[$this->key($consequent)] ?? null;
                if ($supA === null || $supC === null || $supA == 0) continue;

                $confidence = $supportAll / $supA;
                if ($confidence < $this->minConfidence) continue;

                $lift = $confidence / $supC;

                $rules[] = [
                    'antecedents' => $this->bersih($antecedent),
                    'consequents' => $this->bersih($consequent),
                    'support'     => round($supportAll, 6),
                    'confidence'  => round($confidence, 6),
                    'lift'        => round($lift, 6),
                    'kekuatan'    => $this->kekuatan($lift),
                    'interpretasi' => $this->interpretasi(
                        $this->bersih($antecedent),
                        $this->bersih($consequent),
                        $confidence,
                        $lift
                    ),
                ];
            }
        }

        // urutkan lift desc
        usort($rules, fn ($a, $b) => $b['lift'] <=> $a['lift']);
        return $rules;
    }

    /** Semua proper subset non-kosong (selain himpunan penuh). */
    protected function properSubsets(array $items): array
    {
        $subsets = [];
        $total = count($items);
        $max = (1 << $total) - 1;
        for ($mask = 1; $mask < $max; $mask++) {
            $subset = [];
            for ($i = 0; $i < $total; $i++) {
                if ($mask & (1 << $i)) $subset[] = $items[$i];
            }
            $subsets[] = $subset;
        }
        return $subsets;
    }

    protected function key(array $items): string
    {
        $copy = $items;
        sort($copy);
        return implode('||', $copy);
    }

    /** Hilangkan prefix kolom agar tampil rapi. */
    protected function bersih(array $items): string
    {
        $clean = array_map(
            fn ($i) => str_replace(['Ketidaksesuaian=', 'Penyebab='], '', $i),
            $items
        );
        sort($clean);
        return implode(', ', $clean);
    }

    protected function kekuatan(float $lift): string
    {
        if ($lift >= 5) return 'Sangat Kuat';
        if ($lift >= 3) return 'Kuat';
        if ($lift >= 1.5) return 'Sedang';
        return 'Lemah';
    }

    protected function interpretasi(string $a, string $c, float $conf, float $lift): string
    {
        return sprintf(
            'Dari semua kasus "%s", sebanyak %d%% terkait dengan "%s". Kaitan keduanya %s (lift %.1fx).',
            $a, round($conf * 100), $c, strtolower($this->kekuatan($lift)), $lift
        );
    }
}
