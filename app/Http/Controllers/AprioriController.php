<?php

namespace App\Http\Controllers;

use App\Models\ComplaintItem;
use App\Services\AprioriService;
use Illuminate\Http\Request;

class AprioriController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'min_support'    => ['nullable', 'numeric', 'min:0.5', 'max:100'],
            'min_confidence' => ['nullable', 'numeric', 'min:10', 'max:100'],
            'tahun'          => ['nullable', 'integer', 'min:0', 'max:2100'],
            'bulan'          => ['nullable', 'integer', 'min:0', 'max:12'],
        ], [
            'min_support.*'    => 'Minimum support harus berupa angka antara 0,5% dan 100%.',
            'min_confidence.*' => 'Minimum confidence harus berupa angka antara 10% dan 100%.',
            'tahun.*'          => 'Filter tahun harus berupa bilangan bulat antara 0 (semua tahun) dan 2100.',
            'bulan.*'          => 'Filter bulan harus berupa bilangan bulat antara 0 (semua bulan) dan 12.',
        ]);

        // parameter bisa diatur dari form (persen); kosong => default 1,5% / 50%
        $minSupport    = (float) ($data['min_support'] ?? 1.5) / 100;
        $minConfidence = (float) ($data['min_confidence'] ?? 50) / 100;

        // Filter tanggal
        $tahun = $request->integer('tahun');
        $bulan = $request->integer('bulan');

        $q = ComplaintItem::query();
        if ($tahun || $bulan) {
            $q->whereHas('complaint', function ($cq) use ($tahun, $bulan) {
                if ($tahun) $cq->whereYear('tanggal_complain', $tahun);
                if ($bulan) $cq->whereMonth('tanggal_complain', $bulan);
            });
        }
        $items = $q->get();

        // Daftar tahun untuk dropdown
        $years = \App\Models\Complaint::query()
            ->whereNotNull('tanggal_complain')
            ->selectRaw(\App\Models\Complaint::sqlDatePart('YEAR', 'tanggal_complain') . ' as y')
            ->distinct()
            ->orderBy('y')
            ->pluck('y')
            ->all();

        $service = (new AprioriService($minSupport, $minConfidence))->buildTransactions($items);

        $frequent = $service->frequentItemsets();
        $rules    = $service->associationRules($frequent);

        return view('apriori', [
            'frequent'      => $frequent,
            'rules'         => $rules,
            'totalTrx'      => $service->transactionCount(),
            'minSupport'    => $minSupport * 100,
            'minConfidence' => $minConfidence * 100,
            'years'         => $years,
        ]);
    }
}
