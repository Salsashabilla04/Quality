<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintItem;
use App\Services\AprioriService;
use App\Services\SevenToolsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = Complaint::with('items')->get();
        $tools = SevenToolsService::make($data);

        $bundle = SevenToolsService::paretoBundle(
            $data,
            $request->integer('pareto_year') ?: null,
            $request->integer('pareto_month') ?: null,
        );

        $bundleCust = SevenToolsService::paretoBundleCustomer(
            $data,
            $request->integer('pareto_cust_year') ?: null,
            $request->integer('pareto_cust_month') ?: null,
        );

        $apriori = (new AprioriService(0.05, 0.5))->buildTransactions(ComplaintItem::all());
        $rules = $apriori->associationRules();

        $trendType = $request->input('trend_type', 'bulan');

        return view('dashboard', [
            'kpi'              => $tools->kpi(),
            'pareto'           => $bundle['pareto'],
            'paretoFilter'     => $bundle['filter'],
            'paretoCust'       => $bundleCust['pareto'],
            'paretoCustFilter' => $bundleCust['filter'],
            'trend'            => $tools->trendChart($trendType),
            'trendType'        => $trendType,
            'strat'            => $tools->stratifikasi(),
            'topRules'         => array_slice($rules, 0, 5),
            'totalTrx'         => $apriori->transactionCount(),
        ]);
    }
}
