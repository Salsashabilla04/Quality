<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintItem;
use App\Services\AprioriService;
use App\Services\SevenToolsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->user()->role;

        // ── Supervisor / Admin → dashboard khusus manajerial ──────────────
        if (in_array($role, ['supervisor', 'admin'])) {
            return $this->supervisorDashboard($request);
        }

        // ── Staff QA → dashboard operasional (existing) ───────────────────
        $data  = Complaint::with('items')->get();
        $tools = SevenToolsService::make($data);

        $bundle = SevenToolsService::paretoBundle(
            $data,
            $request->integer('pareto_year') ?: null,
            $request->integer('pareto_month') ?: null,
        );

        $bundleDetail = SevenToolsService::paretoBundleDetail(
            $data,
            $request->integer('pareto_detail_year') ?: null,
            $request->integer('pareto_detail_month') ?: null,
        );

        $bundleCust = SevenToolsService::paretoBundleCustomer(
            $data,
            $request->integer('pareto_cust_year') ?: null,
            $request->integer('pareto_cust_month') ?: null,
        );

        $cachedApriori = AprioriService::getCachedResult(0.05, 0.5);
        $rules = $cachedApriori['rules'];

        $trendType = $request->input('trend_type', 'bulan');

        return view('dashboard', [
            'kpi'                => $tools->kpi(),
            'pareto'             => $bundle['pareto'],
            'paretoFilter'       => $bundle['filter'],
            'paretoDetail'       => $bundleDetail['pareto'],
            'paretoDetailFilter' => $bundleDetail['filter'],
            'paretoCust'         => $bundleCust['pareto'],
            'paretoCustFilter'   => $bundleCust['filter'],
            'trend'              => $tools->trendChart($trendType),
            'trendType'          => $trendType,
            'strat'              => $tools->stratifikasi(),
            'topRules'           => array_slice($rules, 0, 5),
            'totalTrx'           => $cachedApriori['totalTrx'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Supervisor Dashboard — data manajerial lengkap
    // ─────────────────────────────────────────────────────────────────────────
    protected function supervisorDashboard(Request $request)
    {
        $data  = Complaint::with('items')->get();
        $tools = SevenToolsService::make($data);
        $kpi   = $tools->kpi();

        // ── NCR Breakdown ──────────────────────────────────────────────────
        $ncrApproved = Complaint::where('supervisor_approval', 'Approved')->count();
        $ncrPending  = Complaint::where('supervisor_approval', 'Pending')->count();
        $ncrRejected = Complaint::where('supervisor_approval', 'Rejected')->count();

        // ── Daftar NCR Pending untuk Validasi Langsung Supervisor ─────────
        $pendingNcrList = Complaint::with('items')
            ->where('supervisor_approval', 'Pending')
            ->orderByDesc('updated_at')
            ->get();

        // ── Upcoming Visits (terdekat dari hari ini, belum selesai) ────────
        $upcomingVisits = Complaint::where('perlu_visit', true)
            ->whereNotNull('tanggal_visit')
            ->where('tanggal_visit', '>=', now()->startOfDay())
            ->orderBy('tanggal_visit', 'asc')
            ->take(5)
            ->get();

        // Jika tidak ada upcoming, ambil yg aktif terbaru
        if ($upcomingVisits->isEmpty()) {
            $upcomingVisits = Complaint::where('perlu_visit', true)
                ->whereNotNull('tanggal_visit')
                ->orderBy('tanggal_visit', 'asc')
                ->take(5)
                ->get();
        }

        // ── Apriori ────────────────────────────────────────────────────────
        $cachedApriori = AprioriService::getCachedResult(0.05, 0.5);
        $topRules = array_slice($cachedApriori['rules'], 0, 5);
        $totalTrx = $cachedApriori['totalTrx'];

        // ── Pareto — semua 4 varian ────────────────────────────────────────
        $bundleJenis = SevenToolsService::paretoBundle(
            $data,
            $request->integer('pareto_year') ?: null,
            $request->integer('pareto_month') ?: null,
        );
        $bundleDetail = SevenToolsService::paretoBundleDetail(
            $data,
            $request->integer('pareto_year') ?: null,
            $request->integer('pareto_month') ?: null,
        );
        $bundleCust = SevenToolsService::paretoBundleCustomer(
            $data,
            $request->integer('pareto_year') ?: null,
            $request->integer('pareto_month') ?: null,
        );
        $bundleCause = SevenToolsService::paretoBundleCause(
            $data,
            $request->integer('pareto_year') ?: null,
            $request->integer('pareto_month') ?: null,
        );



        // ── Laporan Visual (Ringkas — tahun berjalan) ──────────────────────
        $tahunLaporan = now()->year;
        $baseQ = Complaint::query()->whereYear('tanggal_complain', $tahunLaporan);

        $totalComplaint = $data->count();
        $totalOpen      = (clone $baseQ)->where('status', 'Open')->count();
        $totalDiproses  = (clone $baseQ)->where('status', 'Diproses')->count();
        $totalClose     = (clone $baseQ)->where('status', 'Close')->count();
        $totalQtyTahun  = (clone $baseQ)->sum('qty') ?? 0;

        // Trend bulanan tahun ini
        $trendRaw = Complaint::selectRaw('MONTH(tanggal_complain) as bulan_no, COUNT(*) as total')
            ->whereYear('tanggal_complain', $tahunLaporan)
            ->groupBy('bulan_no')
            ->orderBy('bulan_no')
            ->pluck('total', 'bulan_no');

        $namaBulanPendek = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $trendLabels = [];
        $trendData   = [];
        for ($i = 1; $i <= 12; $i++) {
            $trendLabels[] = $namaBulanPendek[$i - 1];
            $trendData[]   = $trendRaw[$i] ?? 0;
        }

        // Top Customer tahun ini
        $topCustomer = (clone $baseQ)
            ->selectRaw('nama_customer, COUNT(*) as total, SUM(qty) as total_qty')
            ->groupBy('nama_customer')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('dashboard-spv', [
            'kpi'            => $kpi,
            // NCR breakdown
            'ncrApproved'    => $ncrApproved,
            'ncrPending'     => $ncrPending,
            'ncrRejected'    => $ncrRejected,
            'pendingNcrList' => $pendingNcrList,
            // Visits
            'upcomingVisits'   => $upcomingVisits,
            'visitActiveTotal' => Complaint::where('perlu_visit', true)->count(),
            // Apriori
            'topRules'       => $topRules,
            'totalTrx'       => $totalTrx,
            // Pareto — 4 varian
            'paretoJenis'    => $bundleJenis['pareto'],
            'paretoDetail'   => $bundleDetail['pareto'],
            'paretoCust'     => $bundleCust['pareto'],
            'paretoCause'    => $bundleCause['pareto'],
            // Laporan Visual
            'tahunLaporan'   => $tahunLaporan,
            'totalComplaint' => $totalComplaint,
            'totalOpen'      => $totalOpen,
            'totalDiproses'  => $totalDiproses,
            'totalClose'     => $totalClose,
            'totalQtyTahun'  => $totalQtyTahun,
            'trendLabels'    => $trendLabels,
            'trendData'      => $trendData,
            'topCustomer'    => $topCustomer,
        ]);
    }
}
