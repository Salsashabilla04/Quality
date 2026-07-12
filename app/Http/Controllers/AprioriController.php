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

        // Baca langsung dari complaint_items, lalu group per complaint_id
        $items = ComplaintItem::all();
        $service = (new AprioriService($minSupport, $minConfidence))->buildTransactions($items);

        $frequent = $service->frequentItemsets();
        $rules    = $service->associationRules($frequent);

        return view('apriori', [
            'frequent'      => $frequent,
            'rules'         => $rules,
            'totalTrx'      => $service->transactionCount(),
            'minSupport'    => $minSupport * 100,
            'minConfidence' => $minConfidence * 100,
        ]);
    }
}
