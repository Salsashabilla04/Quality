<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('access-dashboard');

        $tahun  = $request->integer('tahun') ?: now()->year;
        $bulan  = $request->input('bulan');

        // --- Summary Stats ---
        $baseQ = Complaint::query()->whereYear('tanggal_complain', $tahun);
        if ($bulan) $baseQ->whereMonth('tanggal_complain', $bulan);

        $totalComplaint  = (clone $baseQ)->count();
        $totalClose      = (clone $baseQ)->where('status', 'Close')->count();
        $totalOpen       = (clone $baseQ)->where('status', 'Open')->count();
        $totalDiproses   = (clone $baseQ)->where('status', 'Diproses')->count();
        $totalQty        = (clone $baseQ)->sum('qty') ?? 0;
        $avgLeadTime     = round((clone $baseQ)->whereNotNull('lead_time')->avg('lead_time') ?? 0, 1);

        // --- Trend Bulanan ---
        $trendRaw = Complaint::selectRaw('MONTH(tanggal_complain) as bulan_no, COUNT(*) as total')
            ->whereYear('tanggal_complain', $tahun)
            ->groupBy('bulan_no')
            ->orderBy('bulan_no')
            ->pluck('total', 'bulan_no');

        $namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $trendLabels = [];
        $trendData   = [];
        for ($i = 1; $i <= 12; $i++) {
            $trendLabels[] = $namaBulan[$i - 1];
            $trendData[]   = $trendRaw[$i] ?? 0;
        }

        // --- Top Defect ---
        $defectRaw = \App\Models\ComplaintItem::selectRaw('jenis_ketidaksesuaian, COUNT(*) as total')
            ->whereNotNull('jenis_ketidaksesuaian')
            ->where('jenis_ketidaksesuaian', '!=', '')
            ->whereHas('complaint', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_complain', $tahun);
                if ($bulan) $q->whereMonth('tanggal_complain', $bulan);
            })
            ->groupBy('jenis_ketidaksesuaian')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // --- Top Penyebab ---
        $penyebabRaw = \App\Models\ComplaintItem::selectRaw('penyebab, COUNT(*) as total')
            ->whereNotNull('penyebab')
            ->where('penyebab', '!=', '')
            ->whereHas('complaint', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_complain', $tahun);
                if ($bulan) $q->whereMonth('tanggal_complain', $bulan);
            })
            ->groupBy('penyebab')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // --- Top Customer (by kasus) ---
        $custRaw = (clone $baseQ)
            ->selectRaw('nama_customer, COUNT(*) as total, SUM(qty) as total_qty')
            ->groupBy('nama_customer')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // --- Status Pie ---
        $statusData = [
            'Open'      => $totalOpen,
            'Diproses'  => $totalDiproses,
            'Close'     => $totalClose,
        ];

        // --- Fishbone (Detailed Ishikawa 6M) ---
        $allComplaints = Complaint::with('items')->get();
        $tools         = \App\Services\SevenToolsService::make($allComplaints);
        $fishboneEfek  = $request->input('fishbone_efek', 'AUTO');
        $fishboneLevel = $request->input('fishbone_level', 'detail');
        $fishboneLimit = $request->integer('fishbone_limit', 3);
        $fishbone      = $tools->fishbone($fishboneEfek, $fishboneLevel, $fishboneLimit);

        // --- Apriori Rules ---
        $cachedApriori = \App\Services\AprioriService::getCachedResult(0.05, 0.5);
        $topRules      = array_slice($cachedApriori['rules'], 0, 5);
        $totalTrx      = $cachedApriori['totalTrx'];

        // --- Years available ---
        $years = Complaint::whereNotNull('tanggal_complain')
            ->selectRaw('YEAR(tanggal_complain) as y')
            ->distinct()
            ->orderBy('y')
            ->pluck('y')
            ->all();

        return view('laporan', compact(
            'tahun', 'bulan', 'years', 'namaBulan',
            'totalComplaint', 'totalClose', 'totalOpen', 'totalDiproses',
            'totalQty', 'avgLeadTime',
            'trendLabels', 'trendData',
            'defectRaw', 'penyebabRaw', 'custRaw', 'statusData',
            'fishbone', 'topRules', 'totalTrx'
        ));
    }
}
