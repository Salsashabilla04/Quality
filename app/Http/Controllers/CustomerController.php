<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $tahun = $request->integer('tahun');
        $bulan = $request->integer('bulan');

        $query = Complaint::query()
            ->selectRaw('
                nama_customer, 
                COUNT(*) as total_complaint, 
                SUM(qty) as total_qty, 
                MAX(tanggal_complain) as last_complaint, 
                AVG(lead_time) as avg_lead_time
            ')
            ->groupBy('nama_customer');

        if ($q) {
            $query->where('nama_customer', 'like', "%{$q}%");
        }
        if ($tahun) {
            $query->whereYear('tanggal_complain', $tahun);
        }
        if ($bulan) {
            $query->whereMonth('tanggal_complain', $bulan);
        }

        // Daftar tahun untuk dropdown
        $years = Complaint::query()
            ->whereNotNull('tanggal_complain')
            ->selectRaw('YEAR(tanggal_complain) as y')
            ->distinct()
            ->orderBy('y')
            ->pluck('y')
            ->all();

        $customers = $query->orderByDesc('total_complaint')
            ->paginate(20)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'years' => $years
        ]);
    }
}
