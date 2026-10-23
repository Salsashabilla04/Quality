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

        $cachedApriori = AprioriService::getCachedResult(0.015, 0.5);
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
        $cachedApriori = AprioriService::getCachedResult(0.015, 0.5);
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



        // ── Laporan Visual (Rentang tahun maksimal 10 tahun) ──
        $dbYears = Complaint::whereNotNull('tanggal_complain')
            ->selectRaw('YEAR(tanggal_complain) as y')
            ->distinct()
            ->pluck('y')
            ->map(fn($y) => (int)$y)
            ->all();

        $currentYear = (int) now()->year;
        $dbMin = !empty($dbYears) ? min($dbYears) : $currentYear;
        $dbMax = !empty($dbYears) ? max($dbYears) : $currentYear;
        $startYear = min($dbMin, $currentYear - 5);
        $endYear   = max($dbMax, $currentYear);
        if (($endYear - $startYear + 1) < 10) {
            $startYear = $endYear - 9;
        }
        $laporanYears = range($startYear, $endYear);

        $namaBulan       = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulanPendek = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        // ── Rentang tahun: Dari Tahun → Sampai Tahun (maksimal 10 tahun) ──────
        // 1 tahun  → grafik per bulan; >1 tahun → grafik per tahun
        $maxRange = 10;
        $minYear  = min($laporanYears);
        $maxYear  = max($laporanYears);
        $legacy   = $request->input('laporan_year'); // kompatibel dengan link lama
        $defYear  = ($legacy && $legacy !== 'all') ? (int) $legacy : (in_array(2024, $laporanYears, true) ? 2024 : $currentYear);

        $fromYear = (int) $request->input('laporan_from', $defYear);
        $toYear   = (int) $request->input('laporan_to', $request->has('laporan_from') ? $fromYear : $defYear);
        $fromYear = max($minYear, min($maxYear, $fromYear));
        $toYear   = max($minYear, min($maxYear, $toYear));
        if ($fromYear > $toYear) {
            [$fromYear, $toYear] = [$toYear, $fromYear];
        }
        if ($toYear - $fromYear + 1 > $maxRange) {
            $fromYear = $toYear - $maxRange + 1;
        }
        $isSingleYear = $fromYear === $toYear;
        $selectedYear = $isSingleYear ? (string) $fromYear : 'all'; // dipakai link Laporan PDF


        $baseQ = Complaint::query()
            ->whereYear('tanggal_complain', '>=', $fromYear)
            ->whereYear('tanggal_complain', '<=', $toYear);

        $totalComplaint = (clone $baseQ)->count();
        $totalOpen      = (clone $baseQ)->where('status', 'Open')->count();
        $totalDiproses  = (clone $baseQ)->where('status', 'Diproses')->count();
        $totalClose     = (clone $baseQ)->where('status', 'Close')->count();
        $totalQtyTahun  = (clone $baseQ)->sum('qty') ?? 0;

        $periodeLabel = $isSingleYear ? (string) $fromYear : $fromYear . '–' . $toYear;
        $rangeCount   = $toYear - $fromYear + 1;

        // ── Trend Bulanan (khusus 1 tahun) + pembanding tahun sebelumnya ───────
        $monthlyOf = function (int $year) {
            $raw = Complaint::selectRaw('MONTH(tanggal_complain) as bulan_no, COUNT(*) as total')
                ->whereYear('tanggal_complain', $year)
                ->groupBy('bulan_no')
                ->pluck('total', 'bulan_no');
            $out = [];
            for ($i = 1; $i <= 12; $i++) $out[] = (int) ($raw[$i] ?? 0);
            return $out;
        };
        $trendMonthly     = $isSingleYear ? $monthlyOf($fromYear) : null;
        $trendMonthlyPrev = $isSingleYear ? $monthlyOf($fromYear - 1) : null;

        // ── Trend Tahunan (rentang >1 tahun, maks 10 batang) ────────────────
        $rangeYears  = range($fromYear, $toYear);
        $yearlyRaw   = (clone $baseQ)
            ->selectRaw('YEAR(tanggal_complain) as tahun_no, COUNT(*) as total')
            ->groupBy('tahun_no')
            ->pluck('total', 'tahun_no');
        $trendYearly = array_map(fn($y) => (int) ($yearlyRaw[$y] ?? 0), $rangeYears);

        // ── Bulan tertinggi dalam periode ───────────────────────────────────
        $peak = (clone $baseQ)
            ->selectRaw('YEAR(tanggal_complain) as y, MONTH(tanggal_complain) as m, COUNT(*) as total')
            ->groupBy('y', 'm')
            ->orderByDesc('total')
            ->first();
        $peakMonth = $peak ? $namaBulan[(int) $peak->m] . ($isSingleYear ? '' : ' ' . $peak->y) : null;
        $peakCount = $peak ? (int) $peak->total : 0;

        // ── Rata-rata lead time (complain → close) ─────────────────────────
        $avgLeadTime = (clone $baseQ)
            ->where('status', 'Close')
            ->whereNotNull('tanggal_complain')
            ->selectRaw('AVG(DATEDIFF(COALESCE(approved_at, updated_at), tanggal_complain)) as avg_lt')
            ->value('avg_lt');
        $avgLeadTime = $avgLeadTime !== null ? max(0, round((float) $avgLeadTime, 1)) : null;

        $trendTitle  = 'Tren Komplain';
        $trendLabels = $isSingleYear ? $namaBulanPendek : array_map('strval', $rangeYears);
        $trendData   = $isSingleYear ? $trendMonthly : $trendYearly;

        // Top Customer periode ini
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
            'laporanYears'   => $laporanYears,
            'selectedYear'   => $selectedYear,
            'trendTitle'     => $trendTitle,
            'tahunLaporan'   => $selectedYear,
            'totalComplaint' => $totalComplaint,
            'totalOpen'      => $totalOpen,
            'totalDiproses'  => $totalDiproses,
            'totalClose'     => $totalClose,
            'totalQtyTahun'  => $totalQtyTahun,
            'trendLabels'    => $trendLabels,
            'trendData'      => $trendData,
            'topCustomer'    => $topCustomer,
            'namaBulan'        => $namaBulan,
            'periodeLabel'     => $periodeLabel,
            'fromYear'         => $fromYear,
            'toYear'           => $toYear,
            'maxRange'         => $maxRange,
            'rangeCount'       => $rangeCount,
            'isSingleYear'     => $isSingleYear,
            'currentYear'      => $currentYear,
            'rangeYears'       => $rangeYears,
            'trendMonthly'     => $trendMonthly,
            'trendMonthlyPrev' => $trendMonthlyPrev,
            'trendYearly'      => $trendYearly,
            'peakMonth'        => $peakMonth,
            'peakCount'        => $peakCount,
            'avgLeadTime'      => $avgLeadTime,
        ]);
    }
}
