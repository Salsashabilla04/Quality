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

        // --- CheckSheet mode ---
        $csMode = $request->input('cs_mode', 'jenis_ketidaksesuaian');
        if (!in_array($csMode, ['jenis_ketidaksesuaian', 'detail_ketidaksesuaian'])) {
            $csMode = 'jenis_ketidaksesuaian';
        }

        // --- Pareto Defect filters (periode) ---
        $bundle = SevenToolsService::paretoBundle(
            $data,
            $request->integer('pareto_year') ?: null,
            $request->integer('pareto_month') ?: null,
        );

        // --- Pareto Detail filters (periode) ---
        $bundleDetail = SevenToolsService::paretoBundleDetail(
            $data,
            $request->integer('pareto_detail_year') ?: null,
            $request->integer('pareto_detail_month') ?: null,
        );

        // --- Pareto Customer filters ---
        $bundleCust = SevenToolsService::paretoBundleCustomer(
            $data,
            $request->integer('pareto_cust_year') ?: null,
            $request->integer('pareto_cust_month') ?: null,
        );

        // --- Pareto Penyebab Masalah (BARU) ---
        $bundleCause = SevenToolsService::paretoBundleCause(
            $data,
            $request->integer('pareto_cause_year') ?: null,
            $request->integer('pareto_cause_month') ?: null,
        );

        // --- Fishbone filter (pilih masalah, level detail, & limit penyebab dominan) ---
        $fishboneEfek  = $request->input('fishbone_efek', 'AUTO');
        $fishboneLevel = $request->input('fishbone_level', 'detail');
        $fishboneLimit = $request->integer('fishbone_limit', 3);

        return view('seven-tools', [
            'checkSheet'        => $tools->checkSheetByField($csMode),
            'csMode'            => $csMode,
            'pareto'            => $bundle['pareto'],
            'paretoFilter'      => $bundle['filter'],
            'paretoDetail'      => $bundleDetail['pareto'],
            'paretoDetailFilter'=> $bundleDetail['filter'],
            'paretoCust'        => $bundleCust['pareto'],
            'paretoCustFilter'  => $bundleCust['filter'],
            'paretoCause'       => $bundleCause['pareto'],
            'paretoCauseFilter' => $bundleCause['filter'],
            'histogram'         => $tools->histogram(),
            'controlChart'      => $tools->controlChart(),
            'scatter'           => $tools->scatter(),
            'fishbone'          => $tools->fishbone($fishboneEfek, $fishboneLevel, $fishboneLimit),
            'strat'             => $tools->stratifikasi(),
        ]);
    }
}
