<?php

namespace App\Http\Controllers;

use App\Models\ComplaintItem;
use App\Services\AprioriService;
use Illuminate\Http\Request;

class AprioriController extends Controller
{
    public function index(Request $request)
    {
        // parameter bisa diatur dari form (persen)
        $minSupport    = (float) $request->input('min_support', 5) / 100;
        $minConfidence = (float) $request->input('min_confidence', 50) / 100;
        $minSupport    = min(max($minSupport, 0.01), 1);
        $minConfidence = min(max($minConfidence, 0.01), 1);

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
            ->selectRaw('YEAR(tanggal_complain) as y')
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
