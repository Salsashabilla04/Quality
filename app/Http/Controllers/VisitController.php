<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->integer('tahun', (int) date('Y'));
        $bulan = $request->integer('bulan', (int) date('n'));
        $search = trim($request->input('q', ''));
        $activeTab = $request->input('tab', 'upcoming');

        // Base query for visit records
        $baseQuery = Complaint::where(function ($q) {
            $q->where('perlu_visit', true)
              ->orWhereNotNull('tanggal_visit');
        });

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('nama_customer', 'like', "%{$search}%")
                  ->orWhere('no_customer', 'like', "%{$search}%")
                  ->orWhere('catatan_visit', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 20, 25, 50, 100])) {
            $perPage = 25;
        }

        // 1. Tab Terjadwal:
        $terjadwalVisits = (clone $baseQuery)
            ->where('perlu_visit', true)
            ->orderBy('tanggal_visit', 'asc')
            ->paginate($perPage, ['*'], 'page_terjadwal')
            ->withQueryString();

        // 2. Tab Selesai:
        $selesaiVisits = (clone $baseQuery)
            ->where('perlu_visit', false)
            ->whereNotNull('tanggal_visit')
            ->orderBy('tanggal_visit', 'desc')
            ->paginate($perPage, ['*'], 'page_selesai')
            ->withQueryString();

        // 3. Kalender Kunjungan: memuat agenda visit sesuai bulan & tahun terpilih
        $calendarVisitsData = Complaint::whereNotNull('tanggal_visit')
            ->whereYear('tanggal_visit', $tahun)
            ->whereMonth('tanggal_visit', $bulan)
            ->get();

        $calendarVisits = [];
        foreach ($calendarVisitsData as $v) {
            if ($v->tanggal_visit) {
                $dateKey = $v->tanggal_visit->format('Y-m-d');
                $calendarVisits[$dateKey][] = $v;
            }
        }

        // Years list for filter
        $years = Complaint::selectRaw(\App\Models\Complaint::sqlDatePart('YEAR', 'tanggal_complain') . ' as yr')
            ->distinct()
            ->pluck('yr')
            ->filter()
            ->sortDesc()
            ->values();

        if ($years->isEmpty()) {
            $years = collect([(int) date('Y')]);
        }

        // KPI totals (seluruh data master)
        $totalTerjadwalAll = Complaint::where('perlu_visit', true)->count();
        $totalSelesaiAll   = Complaint::where('perlu_visit', false)->whereNotNull('tanggal_visit')->count();
        $totalAllVisits    = Complaint::where(function ($q) {
            $q->where('perlu_visit', true)
              ->orWhereNotNull('tanggal_visit');
        })->count();

        return view('visit.index', [
            'terjadwalVisits'   => $terjadwalVisits,
            'selesaiVisits'     => $selesaiVisits,
            'calendarVisits'    => $calendarVisits,
            'tahun'             => $tahun,
            'bulan'             => $bulan,
            'years'             => $years,
            'activeTab'         => $activeTab,
            'search'            => $search,
            'totalTerjadwalAll' => $totalTerjadwalAll,
            'totalSelesaiAll'   => $totalSelesaiAll,
            'totalAllVisits'    => $totalAllVisits,
        ]);
    }

    public function schedule(Request $request, Complaint $complaint)
    {
        \Illuminate\Support\Facades\Gate::authorize('approve-ncr');

        $request->validate([
            'tanggal_visit' => ['required', 'date'],
            'jam_visit'     => ['nullable', 'date_format:H:i'],
            'catatan_visit' => ['nullable', 'string', 'max:1000'],
        ]);

        $complaint->update([
            'perlu_visit'   => true,
            'tanggal_visit' => $request->input('tanggal_visit'),
            'jam_visit'     => $request->input('jam_visit'),
            'catatan_visit' => $request->input('catatan_visit'),
        ]);

        return redirect()->route('visit.index')
            ->with('success', "Jadwal visit untuk {$complaint->nama_customer} ({$complaint->no_customer}) berhasil disimpan.");
    }

    public function markDone(Request $request, Complaint $complaint)
    {
        \Illuminate\Support\Facades\Gate::authorize('approve-ncr');

        $complaint->update([
            'perlu_visit'   => false,
            'catatan_visit' => ($complaint->catatan_visit ? $complaint->catatan_visit . ' ' : '') . '[Selesai Kunjungan pada ' . date('d/m/Y H:i') . ']',
        ]);

        return redirect()->back()->with('success', "Kunjungan lapangan untuk {$complaint->nama_customer} ({$complaint->no_customer}) ditandai selesai.");
    }

    public function destroy(Complaint $complaint)
    {
        \Illuminate\Support\Facades\Gate::authorize('approve-ncr');

        $complaint->update([
            'perlu_visit'   => false,
            'tanggal_visit' => null,
            'jam_visit'     => null,
            'catatan_visit' => null,
        ]);

        return redirect()->route('visit.index')
            ->with('success', "Jadwal visit untuk {$complaint->nama_customer} ({$complaint->no_customer}) berhasil dihapus.");
    }
}
