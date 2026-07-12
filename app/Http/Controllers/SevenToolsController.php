<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Services\SevenToolsService;
use Illuminate\Http\Request;

class SevenToolsController extends Controller
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

        return view('seven-tools', [
            'checkSheet'       => $tools->checkSheet(),
            'pareto'           => $bundle['pareto'],
            'paretoFilter'     => $bundle['filter'],
            'paretoCust'       => $bundleCust['pareto'],
            'paretoCustFilter' => $bundleCust['filter'],
            'histogram'        => $tools->histogram(),
            'control'          => $tools->controlChart(),
            'scatter'          => $tools->scatter(),
            'fishbone'         => $tools->fishbone(),
            'strat'            => $tools->stratifikasi(),
        ]);
    }
}
