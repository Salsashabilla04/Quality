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

        // Query complaints with customer visit schedules
        // Active/Scheduled visits FIRST (perlu_visit = true), ordered by nearest visit date.
        // Completed visits (perlu_visit = false) placed at the bottom.
        $visits = Complaint::where('perlu_visit', true)
            ->orWhereNotNull('tanggal_visit')
            ->orderBy('perlu_visit', 'desc')
            ->orderBy('tanggal_visit', 'asc')
            ->get();

        // Group visits by Y-m-d format for calendar rendering
        $calendarVisits = [];
        foreach ($visits as $v) {
            if ($v->tanggal_visit) {
                $dateKey = $v->tanggal_visit->format('Y-m-d');
                $calendarVisits[$dateKey][] = $v;
            }
        }

        // Years list for filter
        $years = Complaint::selectRaw('YEAR(tanggal_complain) as yr')
            ->distinct()
            ->pluck('yr')
            ->filter()
            ->sortDesc()
            ->values();

        if ($years->isEmpty()) {
            $years = collect([(int) date('Y')]);
        }

        return view('visit.index', [
            'visits'         => $visits,
            'calendarVisits' => $calendarVisits,
            'tahun'          => $tahun,
            'bulan'          => $bulan,
            'years'          => $years,
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
}
