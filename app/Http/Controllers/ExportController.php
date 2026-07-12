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

    // ============== EXCEL: DATA COMPLAINT ==============
    public function complaintsExcel(): StreamedResponse
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
        $r = 2;
        foreach (Complaint::with('items')->orderBy('tanggal_complain')->get() as $c) {
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

        return $this->streamXlsx($ss, 'data_complaint_' . date('Ymd') . '.xlsx');
    }

    // ============== PDF: LAPORAN ANALISIS ==============
    public function laporanPdf()
    {
        $data = Complaint::with('items')->get();
        $tools = SevenToolsService::make($data);
        $service = (new AprioriService(0.05, 0.5))->buildTransactions(ComplaintItem::all());

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
        $complaint->load('items');

        $ketTags = $complaint->items->pluck('jenis_ketidaksesuaian')->filter()->unique();
        $penTags = $complaint->items->pluck('penyebab')->filter()->unique();

        // override hanya untuk PDF — TIDAK mengubah data asli
        $penyebab   = $request->filled('penyebab')   ? $request->input('penyebab')   : $penTags->implode(', ');
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

        // Deskripsi: hanya dari detail ketidaksesuaian (mis. L(+)), tanpa kategori
        $deskripsi = $complaint->items->pluck('detail_ketidaksesuaian')->filter()->unique()->implode('; ');
        $efek      = $ketTags->implode(', ') ?: 'Ketidaksesuaian';

        $pdf = Pdf::loadView('exports.ncr', [
            'c'          => $complaint,
            'noNcr'      => $this->nomorNcr($complaint),
            'deskripsi'  => $deskripsi,
            'efek'       => $efek,
            'penyebab'   => $penyebab,
            'correction' => $correction,
            'corrective' => $corrective,
            'fishbone'   => $fishbone,
            'fbLabel'    => ['Man' => 'Man Power', 'Machine' => 'Machine', 'Material' => 'Material',
                             'Method' => 'Method', 'Environment' => 'Environment', 'Measurement' => 'Measurement'],
        ])->setPaper('a4', 'portrait');

        return $pdf->download('NCR_' . str_replace(['/', ' '], '-', $complaint->no_customer ?: $complaint->id) . '.pdf');
    }

    protected function nomorNcr(Complaint $c): string
    {
        $roman = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $bulan = $c->tanggal_complain ? (int) $c->tanggal_complain->format('n') : (int) date('n');
        $tahun = $c->tanggal_complain ? $c->tanggal_complain->format('Y') : date('Y');
        return sprintf('%02d/WBN/QC/NCR/%s/%s', $c->id, $roman[$bulan] ?? '', $tahun);
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
