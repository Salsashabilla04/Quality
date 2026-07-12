<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Complaint extends Model
{
    protected $fillable = [
        'no_customer',
        'nama_customer',
        'tanggal_complain',
        'ukuran',
        'qty',
        'corrective_action',
        'preventive_action',
        'fishbone',
        'tanggal_kirim',
        'tanggal_produksi',
        'area',
        'keterangan',
        'status',
        'lead_time',
    ];

    protected $casts = [
        'tanggal_complain' => 'date',
        'tanggal_kirim'    => 'date',
        'tanggal_produksi' => 'date',
        'qty'              => 'integer',
        'lead_time'        => 'integer',
        'fishbone'         => 'array',
    ];

    /** Urutan kategori 6M untuk diagram fishbone. */
    public const FISHBONE_CATEGORIES = ['Man', 'Machine', 'Material', 'Method', 'Environment', 'Measurement'];

    /**
     * Daftar pilihan untuk dropdown form (diambil dari klasifikasi data PT WBN).
     */
    public const KETIDAKSESUAIAN = [
        'Length_Issue', 'Visual_Defect', 'Deformation', 'ID_Issue',
        'Adhesion_Issue', 'Spec_Issue', 'Moisture_Issue', 'Thickness_Issue',
        'Rough_Cut', 'Printing_Defect', 'Surface_Damage',
    ];

    public const PENYEBAB = [
        'Handling', 'Kondisi Penyimpanan', 'Material Tidak Standar', 'MC Tidak Stabil',
        'Proses Drying Tidak Optimal', 'Material Ex-Stock', 'Kegagalan Perekatan',
        'Settingan Tidak Tepat', 'Human Error', 'Masalah Mesin', 'Masalah Pengukuran',
        'Kesalahan Sambungan Core', 'Kualitas Cutting', 'Dalam monitor',
    ];

    public const KETERANGAN = ['Retur', 'Feedback', '-'];

    public const STATUS = ['Open', 'Close'];

    /**
     * Mapping kategori penyebab -> kategori 6M (Fishbone / Ishikawa).
     */
    public const FISHBONE_6M = [
        'Man'         => ['Human Error', 'Handling', 'Masalah Pengukuran', 'Kesalahan Sambungan Core'],
        'Machine'     => ['Masalah Mesin', 'MC Tidak Stabil', 'Settingan Tidak Tepat'],
        'Material'    => ['Material Tidak Standar', 'Material Ex-Stock', 'Kualitas Cutting'],
        'Method'      => ['Proses Drying Tidak Optimal', 'Kegagalan Perekatan'],
        'Environment' => ['Kondisi Penyimpanan'],
        'Measurement' => ['Dalam monitor'],
    ];

    // ===================== Relationships =====================

    /** Satu complaint punya banyak item cacat/penyebab. */
    public function items(): HasMany
    {
        return $this->hasMany(ComplaintItem::class);
    }

    // ===================== Accessors =====================

    /** Array string jenis ketidaksesuaian (dari relasi items). */
    public function ketidaksesuaianTags(): array
    {
        return $this->items
            ->pluck('jenis_ketidaksesuaian')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** Array string penyebab (dari relasi items). */
    public function penyebabTags(): array
    {
        return $this->items
            ->pluck('penyebab')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    // ===================== Boot =====================

    /**
     * Isi otomatis: no_customer (running number) & lead_time (hari) sebelum disimpan.
     */
    protected static function booted(): void
    {
        static::creating(function (Complaint $c) {
            if (empty($c->no_customer)) {
                $c->no_customer = static::generateNoCustomer($c->nama_customer);
            }
            $c->lead_time = static::hitungLeadTime($c);
        });

        static::updating(function (Complaint $c) {
            $c->lead_time = static::hitungLeadTime($c);
        });
    }

    // ===================== Normalization helpers =====================

    /**
     * Rapikan kategori (Jenis Ketidaksesuaian / Penyebab) agar konsisten & aman dari typo:
     *  - hilangkan spasi berlebih (depan/belakang/ganda)
     *  - jika cocok (case-insensitive) dengan kategori yang sudah ada → pakai ejaan kanonik yang lama
     *  - jika benar-benar baru → Title Case tiap kata, tetap pertahankan akronim pendek (MC, ID, NG, dll)
     *
     * @param array<int,string> $existing daftar kategori yang sudah ada (bawaan + dari DB)
     */
    public static function normalizeCategory(?string $value, array $existing = []): ?string
    {
        if ($value === null) return null;

        $clean = preg_replace('/\s+/', ' ', trim($value));
        if ($clean === '' || $clean === '-') return null;

        // snap ke kategori yang sudah ada (abaikan beda huruf besar/kecil & spasi)
        $needle = mb_strtolower(preg_replace('/\s+/', ' ', $clean));
        foreach ($existing as $opt) {
            if (mb_strtolower(preg_replace('/\s+/', ' ', (string) $opt)) === $needle) {
                return $opt;
            }
        }

        // kategori baru → Title Case, pertahankan akronim pendek (<=3 huruf, semua kapital)
        $words = array_map(function ($w) {
            if ($w === '') return $w;
            if (mb_strlen($w) <= 3 && $w === mb_strtoupper($w)) return $w;
            // simpan token ber-underscore apa adanya (mis. Length_Issue) bila sudah ada huruf kapital
            if (str_contains($w, '_')) return $w;
            return mb_convert_case($w, MB_CASE_TITLE, 'UTF-8');
        }, explode(' ', $clean));

        return implode(' ', $words);
    }

    /**
     * Normalisasi banyak kategori (array) sekaligus: rapikan tiap item, buang kosong & duplikat.
     *
     * @param array<int,string>|string|null $values
     * @param array<int,string> $existing
     * @return array<int,string>
     */
    public static function normalizeCategories($values, array $existing = []): array
    {
        if ($values === null) return [];
        if (is_string($values)) {
            // dukung input dipisah koma
            $values = preg_split('/\s*,\s*/', $values);
        }

        $out = [];
        foreach ((array) $values as $v) {
            $norm = self::normalizeCategory($v, $existing);
            if ($norm !== null && ! in_array($norm, $out, true)) {
                $out[] = $norm;
            }
        }
        return $out;
    }

    /** Kategori 6M untuk sebuah penyebab (default 'Method' bila tak terpetakan). */
    public static function kategoriPenyebab(string $penyebab): string
    {
        foreach (self::FISHBONE_6M as $kategori => $list) {
            if (in_array($penyebab, $list, true)) return $kategori;
        }
        return 'Method';
    }

    /**
     * Bangun struktur fishbone 6M default dari daftar penyebab terpilih.
     * @return array<string, array<int,string>>
     */
    public static function buildFishboneFromPenyebab(array $penyebabList): array
    {
        $fb = array_fill_keys(self::FISHBONE_CATEGORIES, []);
        foreach ($penyebabList as $p) {
            $p = trim((string) $p);
            if ($p === '') continue;
            $kat = self::kategoriPenyebab($p);
            if (! in_array($p, $fb[$kat], true)) $fb[$kat][] = $p;
        }
        return $fb;
    }

    /** Fishbone yang sudah lengkap 6 kategori (untuk ditampilkan/diisi form). */
    public function fishboneNormalized(): array
    {
        $fb = array_fill_keys(self::FISHBONE_CATEGORIES, []);
        foreach ((array) ($this->fishbone ?? []) as $kat => $causes) {
            if (isset($fb[$kat])) {
                $fb[$kat] = array_values(array_filter(array_map('trim', (array) $causes)));
            }
        }
        // bila kosong total, bangun dari penyebab items
        if (! array_filter($fb)) {
            $fb = self::buildFishboneFromPenyebab($this->penyebabTags());
        }
        return $fb;
    }

    // ===================== No Customer =====================

    /**
     * No Customer = [kode customer 2 digit]-[nomor transaksi 3 digit].
     *  - Kode customer: dipakai ulang bila customer sudah pernah ada; jika baru, ambil kode terkecil yang belum dipakai.
     *  - Nomor transaksi: berurutan (maksimum yang ada + 1) — nomor sama menandai 1 transaksi.
     */
    public static function generateNoCustomer(?string $namaCustomer = null): string
    {
        $meta = static::noCustomerMeta();

        $key = mb_strtolower(trim((string) $namaCustomer));
        $code = ($key !== '' && isset($meta['codeByCustomer'][$key]))
            ? $meta['codeByCustomer'][$key]
            : $meta['nextNewCode'];

        return "{$code}-{$meta['nextTrans']}";
    }

    /**
     * Data pendukung penomoran No Customer (dipakai server & preview live di form):
     *  - codeByCustomer: peta nama(lowercase) -> kode 2 digit yang sudah dipakai customer itu
     *  - nextNewCode: kode 2 digit terkecil yang belum dipakai (untuk customer baru)
     *  - nextTrans: nomor transaksi 3 digit berikutnya (maks + 1)
     *
     * @return array{codeByCustomer: array<string,string>, nextNewCode: string, nextTrans: string}
     */
    public static function noCustomerMeta(): array
    {
        $maxTrans = 0;
        $usedCodes = [];
        $codeByCustomer = [];

        foreach (static::query()->whereNotNull('no_customer')->get(['no_customer', 'nama_customer']) as $r) {
            if (preg_match('/^(\d{2})-(\d{3})$/', (string) $r->no_customer, $m)) {
                $maxTrans = max($maxTrans, (int) $m[2]);
                $usedCodes[(int) $m[1]] = true;
                $key = mb_strtolower(trim((string) $r->nama_customer));
                if ($key !== '' && ! isset($codeByCustomer[$key])) {
                    $codeByCustomer[$key] = $m[1];
                }
            }
        }

        $next = 1;
        while (isset($usedCodes[$next])) $next++;

        return [
            'codeByCustomer' => $codeByCustomer,
            'nextNewCode'    => str_pad((string) $next, 2, '0', STR_PAD_LEFT),
            'nextTrans'      => str_pad((string) ($maxTrans + 1), 3, '0', STR_PAD_LEFT),
        ];
    }

    // ===================== Internal helpers =====================

    protected static function hitungLeadTime(Complaint $c): ?int
    {
        if ($c->tanggal_complain && $c->tanggal_produksi) {
            $produksi = $c->tanggal_produksi instanceof Carbon ? $c->tanggal_produksi : Carbon::parse($c->tanggal_produksi);
            $complain = $c->tanggal_complain instanceof Carbon ? $c->tanggal_complain : Carbon::parse($c->tanggal_complain);
            return $produksi->diffInDays($complain, false);
        }
        return null;
    }
}
