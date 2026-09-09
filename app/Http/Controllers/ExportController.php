<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintItem;
use App\Services\AprioriService;
use App\Services\SevenToolsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    // ============== EXCEL: HASIL APRIORI ==============
    public function aprioriExcel(): StreamedResponse
    {
        $service = (new AprioriService(0.05, 0.5))->buildTransactions(ComplaintItem::all());
        $frequent = $service->frequentItemsets();
        $rules = $service->associationRules($frequent);

        $ss = new Spreadsheet();

        // Sheet 1: Frequent Itemsets
        $s1 = $ss->getActiveSheet();
        $s1->setTitle('Frequent Itemsets');
        $this->headerRow($s1, ['Support', 'Jumlah', 'Size', 'Itemset']);
        $r = 2;
        foreach ($frequent as $f) {
            $items = implode(', ', array_map(
                fn ($i) => str_replace(['Ketidaksesuaian=', 'Penyebab='], '', $i),
                $f['items']
            ));
            $s1->fromArray([round($f['support'] * 100, 2) . '%', $f['count'], $f['size'], $items], null, "A{$r}");
            $r++;
        }
        foreach (['A', 'B', 'C', 'D'] as $col) $s1->getColumnDimension($col)->setAutoSize(true);

        // Sheet 2: Association Rules
        $s2 = $ss->createSheet();
        $s2->setTitle('Association Rules');
        $this->headerRow($s2, ['Antecedent (Jika)', 'Consequent (Maka)', 'Support', 'Confidence', 'Lift', 'Kekuatan', 'Interpretasi']);
        $r = 2;
        foreach ($rules as $rule) {
            $s2->fromArray([
                $rule['antecedents'], $rule['consequents'],
                round($rule['support'] * 100, 2) . '%',
                round($rule['confidence'] * 100, 2) . '%',
                round($rule['lift'], 2), $rule['kekuatan'], $rule['interpretasi'],
            ], null, "A{$r}");
            $r++;
        }
        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) $s2->getColumnDimension($col)->setAutoSize(true);
        $s2->getColumnDimension('G')->setWidth(70);

        return $this->streamXlsx($ss, 'hasil_apriori_' . date('Ymd') . '.xlsx');
    }

    // Helper filter query untuk Excel & PDF
    protected function buildFilteredQuery(Request $request)
    {
        $query = Complaint::with('items');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('no_customer', 'like', "%{$q}%")
                    ->orWhere('nama_customer', 'like', "%{$q}%")
                    ->orWhere('area', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%")
                    ->orWhereHas('items', function ($iq) use ($q) {
                        $iq->where('jenis_ketidaksesuaian', 'like', "%{$q}%")
                           ->orWhere('detail_ketidaksesuaian', 'like', "%{$q}%")
                           ->orWhere('penyebab', 'like', "%{$q}%")
                           ->orWhere('detail_penyebab', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('customer')) {
            $query->where('nama_customer', $request->input('customer'));
        }

        if ($request->filled('tahun') && $request->input('tahun') !== 'all') {
            $query->whereYear('tanggal_complain', $request->integer('tahun'));
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_complain', $request->integer('bulan'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('approval')) {
            $query->where('supervisor_approval', $request->input('approval'));
        }

        if ($request->boolean('anomali')) {
            $query->whereNotNull('tanggal_complain')
                  ->whereNotNull('tanggal_produksi')
                  ->whereColumn('tanggal_complain', '<', 'tanggal_produksi');
        }

        return $query;
    }

    // ============== EXCEL: DATA COMPLAINT (FILTERABLE) ==============
    public function complaintsExcel(Request $request): StreamedResponse
    {
        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Data Complaint');
        $this->headerRow($sheet, [
            'No Customer', 'Nama Customer', 'Tgl Complain', 'Ukuran', 'Qty',
            'Ketidaksesuaian', 'Detail', 'Penyebab', 'Detail Penyebab',
            'Corrective Action', 'Preventive Action', 'Tgl Kirim', 'Tgl Produksi',
            'Lead Time (hari)', 'Area', 'Keterangan', 'Status',
        ]);

        $complaints = $this->buildFilteredQuery($request)->orderBy('tanggal_complain', 'desc')->get();
        $r = 2;
        foreach ($complaints as $c) {
            $ketTags = $c->items->pluck('jenis_ketidaksesuaian')->filter()->unique()->implode(', ');
            $detKet  = $c->items->pluck('detail_ketidaksesuaian')->filter()->unique()->implode('; ');
            $penTags = $c->items->pluck('penyebab')->filter()->unique()->implode(', ');
            $detPen  = $c->items->pluck('detail_penyebab')->filter()->unique()->implode('; ');

            $sheet->fromArray([
                $c->no_customer, $c->nama_customer, optional($c->tanggal_complain)->format('Y-m-d'),
                $c->ukuran, $c->qty, $ketTags, $detKet,
                $penTags, $detPen, $c->corrective_action, $c->preventive_action,
                optional($c->tanggal_kirim)->format('Y-m-d'), optional($c->tanggal_produksi)->format('Y-m-d'),
                $c->lead_time, $c->area, $c->keterangan, $c->status,
            ], null, "A{$r}");
            $r++;
        }
        foreach (range('A', 'Q') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $filenameSuffix = '';
        if ($request->filled('customer')) $filenameSuffix .= '_' . \Str::slug($request->input('customer'));
        if ($request->filled('tahun')) $filenameSuffix .= '_' . $request->integer('tahun');
        if ($request->filled('bulan')) $filenameSuffix .= '_bln' . $request->integer('bulan');

        return $this->streamXlsx($ss, 'data_complaint' . ($filenameSuffix ?: '_' . date('Ymd')) . '.xlsx');
    }

    // ============== PDF: LAPORAN ANALISIS (FILTERABLE) ==============
    public function laporanPdf(Request $request)
    {
        $data = $this->buildFilteredQuery($request)->get();
        $tools = SevenToolsService::make($data);
        $service = (new AprioriService(0.05, 0.5))->buildTransactions(ComplaintItem::whereIn('complaint_id', $data->pluck('id'))->get());

        $pdf = Pdf::loadView('exports.laporan', [
            'kpi'        => $tools->kpi(),
            'pareto'     => $tools->pareto(),
            'paretoCause'=> $tools->pareto('penyebab'),
            'fishbone'   => $tools->fishbone(),
            'frequent'   => $service->frequentItemsets(),
            'rules'      => $service->associationRules(),
            'totalTrx'   => $service->transactionCount(),
            'tanggal'    => now()->format('d F Y'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan_analisis_' . date('Ymd') . '.pdf');
    }

    // ============== PDF: SURAT NCR per complaint (hal.1 form + hal.2 fishbone) ==============
    public function ncrPdf(Request $request, Complaint $complaint)
    {
        if ($complaint->supervisor_approval !== 'Approved') {
            return redirect()->back()
                ->with('error', "Surat NCR PDF untuk {$complaint->no_customer} hanya dapat diunduh setelah disetujui (Approved) oleh Supervisor QC.");
        }

        $complaint->load('items');

        $ketTags = $complaint->items->pluck('jenis_ketidaksesuaian')->filter()->unique();
        $penTags = $complaint->items->pluck('penyebab')->filter()->unique();

        // override hanya untuk PDF — TIDAK mengubah data asli
        $penyebab   = $request->filled('penyebab')   ? $request->input('penyebab')   : ($complaint->deskripsi_penyebab ?: $penTags->implode(', '));
        $correction = $request->filled('correction') ? $request->input('correction') : (string) $complaint->corrective_action;
        $corrective = $request->filled('corrective') ? $request->input('corrective') : (string) $complaint->preventive_action;

        // fishbone: dari input form (override) atau data complaint
        $fbInput = (array) $request->input('fishbone', []);
        if (array_filter($fbInput)) {
            $fishbone = [];
            foreach (Complaint::FISHBONE_CATEGORIES as $kat) {
                $raw = (string) ($fbInput[$kat] ?? '');
                $fishbone[$kat] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw))));
            }
        } else {
            $fishbone = $complaint->fishboneNormalized();
        }

        // Deskripsi: utamakan deskripsi_customer jika ada, atau detail_ketidaksesuaian
        $deskripsi = $complaint->deskripsi_customer ?: $complaint->items->pluck('detail_ketidaksesuaian')->filter()->unique()->implode('; ');
        $efek      = $ketTags->implode(', ') ?: 'Ketidaksesuaian';

        $noSuratFormatted = $this->nomorNcr($complaint);

        $pdf = Pdf::loadView('exports.ncr', [
            'c'          => $complaint,
            'noNcr'      => $noSuratFormatted,
            'deskripsi'  => $deskripsi,
            'efek'       => $efek,
            'penyebab'   => $penyebab,
            'correction' => $correction,
            'corrective' => $corrective,
            'fishbone'   => $fishbone,
            'fbLabel'    => ['Man' => 'Man Power', 'Machine' => 'Machine', 'Material' => 'Material',
                             'Method' => 'Method', 'Environment' => 'Environment', 'Measurement' => 'Measurement'],
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Surat_NCR_' . str_replace(['/', ' '], '_', $noSuratFormatted) . '.pdf');
    }

    protected function nomorNcr(Complaint $c): string
    {
        $tahun = $c->tanggal_complain ? $c->tanggal_complain->format('Y') : date('Y');

        // Hitung nomor urut surat NCR yang terbit di tahun berjalan (reset tiap pergantian tahun)
        $seq = Complaint::whereYear('tanggal_complain', $tahun)
            ->where(function ($q) use ($c) {
                $q->where('tanggal_complain', '<', $c->tanggal_complain)
                  ->orWhere(function ($q2) use ($c) {
                      $q2->where('tanggal_complain', $c->tanggal_complain)
                         ->where('id', '<=', $c->id);
                  });
            })
            ->count();

        if ($seq === 0) {
            $seq = 1;
        }

        return sprintf('%03d/NCR/QA/%s', $seq, $tahun);
    }

    // ===================== helper =====================
    protected function headerRow($sheet, array $headers): void
    {
        $sheet->fromArray($headers, null, 'A1');
        $lastCol = chr(64 + count($headers));
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A1:{$lastCol}1")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0EA5E9');
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function streamXlsx(Spreadsheet $ss, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
