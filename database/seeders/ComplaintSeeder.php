<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\ComplaintItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/seed_complaints.json');
        if (! file_exists($path)) {
            $this->command->error("File seed tidak ditemukan: {$path}");
            return;
        }

        $rows = json_decode(file_get_contents($path), true) ?? [];

        // Normalisasi nilai keterangan/status agar konsisten
        $normKeterangan = function (?string $v): ?string {
            $v = trim((string) $v);
            if ($v === '' || $v === '-') return null;
            if (stripos($v, 'retur') !== false) return 'Retur';
            if (stripos($v, 'feedback') !== false) return 'Feedback';
            return $v;
        };
        $normStatus = function (?string $v): string {
            $v = trim((string) $v);
            return stripos($v, 'close') !== false ? 'Close' : 'Open';
        };

        Complaint::query()->delete();
        // ComplaintItem juga terhapus via cascade

        // ===== Group by no_customer =====
        // Baris-baris dengan no_customer sama = 1 transaksi dengan multi-cacat
        $groups = [];
        foreach ($rows as $r) {
            $no = $r['no_customer'] ?? null;
            if (! $no) continue;
            $groups[$no][] = $r;
        }

        $count = 0;
        foreach ($groups as $no => $groupRows) {
            $first = $groupRows[0]; // ambil data header dari baris pertama

            $tglComplain = ! empty($first['tanggal_complain']) ? Carbon::parse($first['tanggal_complain']) : null;
            $tglProduksi = ! empty($first['tanggal_produksi']) ? Carbon::parse($first['tanggal_produksi']) : null;

            if (! $tglComplain) {
                continue; // tanggal complain wajib
            }

            $leadTime = ($tglComplain && $tglProduksi)
                ? $tglProduksi->diffInDays($tglComplain, false)
                : null;

            // Insert 1 baris ke complaints (header transaksi)
            $complaint = Complaint::create([
                'no_customer'      => $first['no_customer'] ?? null,
                'nama_customer'    => $first['nama_customer'] ?? 'Tidak diketahui',
                'tanggal_complain' => $tglComplain,
                'ukuran'           => $first['ukuran'] ?? null,
                'qty'              => $first['qty'] ?? null,
                'corrective_action' => $first['corrective_action'] ?? null,
                'preventive_action' => $first['preventive_action'] ?? null,
                'tanggal_kirim'    => ! empty($first['tanggal_kirim']) ? Carbon::parse($first['tanggal_kirim']) : null,
                'tanggal_produksi' => $tglProduksi,
                'area'             => $first['area'] ?? null,
                'keterangan'       => $normKeterangan($first['keterangan'] ?? null),
                'status'           => $normStatus($first['status'] ?? null),
                'lead_time'        => $leadTime,
            ]);

            // Insert N baris ke complaint_items (cacat & penyebab)
            foreach ($groupRows as $r) {
                $ket = trim((string) ($r['apriori_ketidaksesuaian'] ?? ''));
                $pen = trim((string) ($r['apriori_penyebab'] ?? ''));

                if ($ket === '' && $pen === '') continue;

                $complaint->items()->create([
                    'jenis_ketidaksesuaian'  => $ket ?: null,
                    'detail_ketidaksesuaian' => $r['detail_ketidaksesuaian'] ?? null,
                    'penyebab'               => $pen ?: null,
                    'detail_penyebab'        => $r['detail_penyebab'] ?? null,
                ]);
            }

            $count++;
        }

        $totalItems = ComplaintItem::count();
        $this->command->info("Berhasil import {$count} transaksi ({$totalItems} items) dari seed_complaints.json.");
    }
}
