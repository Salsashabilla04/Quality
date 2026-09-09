<?php

namespace App\Http\Controllers;

use App\Models\ComplaintItem;
use App\Models\DefectDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DefectDictionaryController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('access-dashboard');

        // Pastikan taksonomi definisi default terisi jika tabel masih kosong
        DefectDictionary::seedDefaultsIfEmpty();

        $q = trim((string) $request->input('q'));

        // Ambil data referensi deskripsi & saran standar dari database
        $standardDefects = DefectDictionary::all()->keyBy(function ($item) {
            return strtolower(trim($item->jenis_ketidaksesuaian));
        });

        // Query riwayat aktual complaint items dari database
        $items = ComplaintItem::select('jenis_ketidaksesuaian', 'detail_ketidaksesuaian', 'penyebab', 'detail_penyebab')->get();

        $stylePresets = [
            'length_issue'                => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-800', 'icon' => '📏'],
            'deformation'                 => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'badge' => 'bg-amber-100 text-amber-800', 'icon' => '📐'],
            'visual_defect'               => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'border-teal-200', 'badge' => 'bg-teal-100 text-teal-800', 'icon' => '👁️'],
            'id_issue'                    => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'badge' => 'bg-indigo-100 text-indigo-800', 'icon' => '⭕'],
            'adhesion_issue'              => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'badge' => 'bg-rose-100 text-rose-800', 'icon' => '🔗'],
            'spec_issue'                  => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'badge' => 'bg-sky-100 text-sky-800', 'icon' => '📋'],
            'moisture_issue'              => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-700', 'border' => 'border-cyan-200', 'badge' => 'bg-cyan-100 text-cyan-800', 'icon' => '💧'],
            'thickness_issue'             => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200', 'badge' => 'bg-violet-100 text-violet-800', 'icon' => '📊'],
            'rough_cut'                   => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'badge' => 'bg-orange-100 text-orange-800', 'icon' => '✂️'],
            'printing_defect'             => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'badge' => 'bg-purple-100 text-purple-800', 'icon' => '🖨️'],
            'surface_damage'              => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-300', 'badge' => 'bg-slate-200 text-slate-800', 'icon' => '🛡️'],
            // Penyebab presets
            'handling'                    => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'badge' => 'bg-amber-100 text-amber-800', 'icon' => '📦'],
            'kondisi_penyimpanan'         => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'badge' => 'bg-sky-100 text-sky-800', 'icon' => '🏢'],
            'material_tidak_standar'      => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'badge' => 'bg-rose-100 text-rose-800', 'icon' => '📄'],
            'mc_tidak_stabil'             => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'badge' => 'bg-indigo-100 text-indigo-800', 'icon' => '⚙️'],
            'proses_drying_tidak_optimal' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'badge' => 'bg-orange-100 text-orange-800', 'icon' => '🔥'],
            'material_ex-stock'           => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'border-teal-200', 'badge' => 'bg-teal-100 text-teal-800', 'icon' => '🏭'],
            'kegagalan_perekatan'         => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200', 'badge' => 'bg-violet-100 text-violet-800', 'icon' => '🧪'],
            'settingan_tidak_tepat'       => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-700', 'border' => 'border-cyan-200', 'badge' => 'bg-cyan-100 text-cyan-800', 'icon' => '🔧'],
            'human_error'                 => ['bg' => 'bg-pink-50', 'text' => 'text-pink-700', 'border' => 'border-pink-200', 'badge' => 'bg-pink-100 text-pink-800', 'icon' => '👤'],
            'masalah_mesin'               => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-800', 'icon' => '🛠️'],
            'masalah_pengukuran'          => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'badge' => 'bg-purple-100 text-purple-800', 'icon' => '📐'],
        ];

        // 1. Agregasi Ketidaksesuaian (Jenis -> Detail)
        $ketMap = [];
        foreach ($items as $item) {
            $raw = trim((string) $item->jenis_ketidaksesuaian);
            if ($raw === '' || in_array(strtolower($raw), ['-', 'nan', 'null'])) continue;

            $key = strtolower(str_replace(' ', '_', $raw));
            if (! isset($ketMap[$key])) {
                $ketMap[$key] = [
                    'key'         => $key,
                    'jenis'       => $raw,
                    'jenis_label' => ucwords(str_replace('_', ' ', $raw)),
                    'total'       => 0,
                    'details_map' => [],
                    'style'       => $stylePresets[$key] ?? ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'badge' => 'bg-sky-100 text-sky-800', 'icon' => '🏷️'],
                ];
            }
            $ketMap[$key]['total']++;

            $det = trim((string) $item->detail_ketidaksesuaian);
            if ($det !== '' && ! in_array(strtolower($det), ['-', 'nan', 'null', 'none'])) {
                $ketMap[$key]['details_map'][$det] = ($ketMap[$key]['details_map'][$det] ?? 0) + 1;
            }
        }

        foreach ($ketMap as &$row) {
            arsort($row['details_map']);
            $details = [];
            foreach ($row['details_map'] as $name => $count) {
                $details[] = ['name' => $name, 'count' => $count];
            }
            $row['details'] = $details;
        }
        unset($row);

        uasort($ketMap, fn ($a, $b) => $b['total'] <=> $a['total']);
        $ketidaksesuaianList = array_values($ketMap);

        // 2. Agregasi Penyebab (Jenis Penyebab -> Detail Penyebab)
        $penMap = [];
        foreach ($items as $item) {
            $raw = trim((string) $item->penyebab);
            if ($raw === '' || in_array(strtolower($raw), ['-', 'nan', 'null'])) continue;

            $key = strtolower(str_replace(' ', '_', $raw));
            if (! isset($penMap[$key])) {
                $penMap[$key] = [
                    'key'         => $key,
                    'jenis'       => $raw,
                    'jenis_label' => ucwords(str_replace('_', ' ', $raw)),
                    'total'       => 0,
                    'details_map' => [],
                    'style'       => $stylePresets[$key] ?? ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'badge' => 'bg-amber-100 text-amber-800', 'icon' => '🛠️'],
                ];
            }
            $penMap[$key]['total']++;

            $det = trim((string) $item->detail_penyebab);
            if ($det !== '' && ! in_array(strtolower($det), ['-', 'nan', 'null', 'none'])) {
                $penMap[$key]['details_map'][$det] = ($penMap[$key]['details_map'][$det] ?? 0) + 1;
            }
        }

        foreach ($penMap as &$row) {
            arsort($row['details_map']);
            $details = [];
            foreach ($row['details_map'] as $name => $count) {
                $details[] = ['name' => $name, 'count' => $count];
            }
            $row['details'] = $details;
        }
        unset($row);

        uasort($penMap, fn ($a, $b) => $b['total'] <=> $a['total']);
        $penyebabList = array_values($penMap);

        return view('defect-dictionary.index', [
            'ketidaksesuaianList' => $ketidaksesuaianList,
            'penyebabList'        => $penyebabList,
            'q'                   => $q,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-complaints');

        $validated = $request->validate([
            'jenis_ketidaksesuaian' => 'required|string|max:255|unique:defect_dictionaries,jenis_ketidaksesuaian',
            'definisi'              => 'nullable|string',
            'tindakan_rekomendasi'  => 'nullable|string',
        ]);

        DefectDictionary::create($validated);

        return redirect()->route('defect-dictionary.index')->with('success', 'Definisi kamus cacat berhasil ditambahkan.');
    }

    public function update(Request $request, DefectDictionary $defectDictionary)
    {
        Gate::authorize('manage-complaints');

        $validated = $request->validate([
            'definisi'             => 'nullable|string',
            'tindakan_rekomendasi' => 'nullable|string',
        ]);

        $defectDictionary->update($validated);

        return redirect()->route('defect-dictionary.index')->with('success', 'Definisi dan rekomendasi cacat berhasil diperbarui.');
    }

    public function apiLookup(Request $request)
    {
        $term = trim((string) $request->input('term'));
        if ($term === '') {
            return response()->json(['success' => false, 'data' => null]);
        }

        $def = DefectDictionary::where('jenis_ketidaksesuaian', 'like', "%{$term}%")->first();

        return response()->json([
            'success' => (bool) $def,
            'data'    => $def,
        ]);
    }
}