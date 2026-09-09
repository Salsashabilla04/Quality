<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportController extends Controller
{
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            // Gunakan formatData = false agar nilai tanggal (date) dikembalikan sebagai angka serial Excel murni
            $rows = $worksheet->toArray(null, true, false, false);

            $importedCount = 0;
            $itemsCount = 0;
            $unregisteredCustomers = [];
            
            // 1. Cari baris header (yang mengandung 'Nama Customer')
            $headerRowIndex = -1;
            $colMap = [];
            foreach ($rows as $index => $row) {
                // Bersihkan semua nilai baris untuk pengecekan
                $cleanRow = array_map(function($v) { return strtolower(trim((string)$v)); }, $row);
                if (in_array('nama customer', $cleanRow)) {
                    $headerRowIndex = $index;
                    // Mapping nama kolom ke index array
                    foreach ($cleanRow as $colIdx => $colName) {
                        if ($colName !== '') {
                            $colMap[$colName] = $colIdx;
                        }
                    }
                    break;
                }
            }

            if ($headerRowIndex === -1 || !isset($colMap['nama customer'])) {
                return redirect()->route('complaints.index')->with('error', 'Gagal: Format Excel tidak valid. Pastikan ada baris header dengan kolom "Nama Customer".');
            }

            // Fungsi helper untuk mengambil nilai dari baris menggunakan pencarian nama header
            $getVal = function($row, array $possibleNames) use ($colMap) {
                foreach ($possibleNames as $name) {
                    if (isset($colMap[$name])) {
                        return $row[$colMap[$name]] ?? null;
                    }
                }
                return null;
            };

            // Ambil meta customer untuk memvalidasi Master Customer
            $meta = Complaint::noCustomerMeta();
            $codeByCustomer = $meta['codeByCustomer'];

            // 2. Loop data dimulai dari setelah baris header
            for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                $namaCustomer = trim((string)$getVal($row, ['nama customer']));
                if (empty($namaCustomer)) {
                    continue;
                }

                // Catat jika ada customer baru yang otomatis didaftarkan
                $key = mb_strtolower($namaCustomer);
                if (!isset($codeByCustomer[$key])) {
                    if (!in_array($namaCustomer, $unregisteredCustomers)) {
                        $unregisteredCustomers[] = $namaCustomer;
                    }
                }

                // Parsing tanggal
                $tglComplain = $this->parseDate($getVal($row, ['tanggal complain', 'tgl complain']));
                $tglKirim = $this->parseDate($getVal($row, ['tanggal kirim', 'tgl kirim']));
                $tglProduksi = $this->parseDate($getVal($row, ['tanggal produksi', 'tgl produksi']));

                // Buat data complaint baru
                $complaint = new Complaint();
                $complaint->user_id = auth()->id();
                $complaint->no_customer = Complaint::generateNoCustomer($namaCustomer);
                $importedCount++;

                $complaint->nama_customer = $namaCustomer;
                $complaint->tanggal_complain = $tglComplain ?: now(); // fallback to now if empty
                $complaint->ukuran = trim((string)$getVal($row, ['ukuran', 'ukuran ']));
                $complaint->qty = (int)$getVal($row, ['qty', 'kuantitas']);
                
                $complaint->corrective_action = trim((string)$getVal($row, ['corrective action', 'tindakan koreksi']));
                $complaint->preventive_action = trim((string)$getVal($row, ['preventive action', 'tindakan korektif']));
                
                $complaint->tanggal_kirim = $tglKirim;
                $complaint->tanggal_produksi = $tglProduksi;
                
                $complaint->area = trim((string)$getVal($row, ['area']));
                $complaint->keterangan = trim((string)$getVal($row, ['keterangan']));
                $statusStr = trim((string)$getVal($row, ['status']));
                $complaint->status = (strtolower($statusStr) === 'close') ? 'Close' : 'Open';

                $complaint->save();

                $ketStr = (string)$getVal($row, ['ketidaksesuaian', 'apriori ketidaksesuain', 'apriori ketidaksesuaian']);
                $detKetStr = (string)$getVal($row, ['detail ketidaksesuaian', 'detail']);
                $penStr = (string)$getVal($row, ['penyebab', 'apriori penyebab']);
                $detPenStr = (string)$getVal($row, ['detail penyebab']);

                $ketTags = array_map('trim', explode(',', $ketStr));
                $detKet = array_map('trim', explode(';', $detKetStr));
                $penTags = array_map('trim', explode(',', $penStr));
                $detPen = array_map('trim', explode(';', $detPenStr));

                $maxItems = max(count(array_filter($ketTags)), count(array_filter($penTags)));
                if ($maxItems === 0) {
                    $complaint->items()->create([
                        'jenis_ketidaksesuaian' => null,
                        'detail_ketidaksesuaian' => null,
                        'penyebab' => null,
                        'detail_penyebab' => null,
                    ]);
                    $itemsCount++;
                } else {
                    $ketExisting = Complaint::KETIDAKSESUAIAN;
                    $penExisting = Complaint::PENYEBAB;
                    
                    for ($j = 0; $j < $maxItems; $j++) {
                        $kV = $ketTags[$j] ?? ($ketTags[0] ?? null);
                        $pV = $penTags[$j] ?? ($penTags[0] ?? null);
                        
                        $complaint->items()->create([
                            'jenis_ketidaksesuaian' => Complaint::normalizeCategory($kV, $ketExisting),
                            'detail_ketidaksesuaian' => $detKet[$j] ?? ($detKet[0] ?? null),
                            'penyebab' => Complaint::normalizeCategory($pV, $penExisting),
                            'detail_penyebab' => $detPen[$j] ?? ($detPen[0] ?? null),
                        ]);
                        $itemsCount++;
                    }
                }
                
                // Normalisasi fishbone bila tidak ada fishbone lama
                $penyebabList = $complaint->items()->pluck('penyebab')->filter()->unique()->all();
                $complaint->fishbone = Complaint::buildFishboneFromPenyebab($penyebabList);
                $complaint->save();
            }

            // Rekalkulasi penomoran urut agar kronologis sesuai tanggal_complain
            Complaint::recalculateTransactionNumbers();
            \App\Services\AprioriService::clearCache();

            $successMessage = "Berhasil mengimpor {$importedCount} data complaint dan {$itemsCount} detail item.";
            if (count($unregisteredCustomers) > 0) {
                $names = implode(', ', $unregisteredCustomers);
                $successMessage .= " Catatan: Customer baru otomatis didaftarkan: {$names}.";
            }

            return redirect()->route('complaints.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            return redirect()->route('complaints.index')
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Parsing tanggal dari format Excel (angka serial) atau string Y-m-d.
     */
    private function parseDate($value): ?Carbon
    {
        if (empty($value)) return null;

        if (is_numeric($value)) {
            try {
                return Carbon::instance(Date::excelToDateTimeObject($value));
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            // Coba beberapa format umum jika Carbon::parse gagal
            $formats = ['d/m/Y', 'd-m-Y', 'd/m/y', 'd-m-y'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value)->startOfDay();
                } catch (\Exception $ex) {
                    continue;
                }
            }
            return null;
        }
    }
}
