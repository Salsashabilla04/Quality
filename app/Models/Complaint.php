<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
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
        'foto_bukti',
        'deskripsi_customer',
        'deskripsi_penyebab',
        'keterangan',
        'status',
        'supervisor_approval',
        'catatan_supervisor',
        'approved_at',
        'perlu_visit',
        'tanggal_visit',
        'jam_visit',
        'catatan_visit',
        'lead_time',
    ];

    protected $casts = [
        'tanggal_complain' => 'date',
        'tanggal_kirim'    => 'date',
        'tanggal_produksi' => 'date',
        'tanggal_visit'    => 'date',
        'approved_at'       => 'datetime',
        'perlu_visit'      => 'boolean',
        'qty'              => 'integer',
        'lead_time'        => 'integer',
        'fishbone'         => 'array',
        'foto_bukti'       => 'array',
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

    public const STATUS = ['Open', 'Diproses', 'Close'];

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

    /**
     * Kamus pemetaan: Detail Ketidaksesuaian → Jenis Ketidaksesuaian (Apriori).
     * Dibangun dari 200+ data histori komplain PT WBN.
     */
    public const DETAIL_TO_JENIS_MAP = [
        // Length_Issue
        'L(-)'       => 'Length_Issue',
        'L(+)'       => 'Length_Issue',
        'L Variasi'  => 'Length_Issue',

        // Visual_Defect
        'Visual Kotor'              => 'Visual_Defect',
        'NG Visual'                 => 'Visual_Defect',
        'Visual Inner Label Sobek'  => 'Visual_Defect',
        'Inner Berminyak'           => 'Visual_Defect',

        // Deformation
        'Dented'        => 'Deformation',
        'Oval'          => 'Deformation',
        'Bending'       => 'Deformation',
        'Bergelombang'  => 'Deformation',

        // ID_Issue
        'ID(+)'           => 'ID_Issue',
        'ID(-)'           => 'ID_Issue',
        'ID Tidak Rata'   => 'ID_Issue',
        'Seret'           => 'ID_Issue',
        'ID Come Down'    => 'ID_Issue',

        // Adhesion_Issue
        'Peel Off'          => 'Adhesion_Issue',
        'Peel Off Inner'    => 'Adhesion_Issue',
        'Peel Off Outer'    => 'Adhesion_Issue',
        'Unstuck Inner'     => 'Adhesion_Issue',
        'Unstuck Body'      => 'Adhesion_Issue',
        'Unstuck Outer'     => 'Adhesion_Issue',
        'Unstuck Top Ply'   => 'Adhesion_Issue',
        'Lem Tidak Kuat'    => 'Adhesion_Issue',

        // Spec_Issue
        'Overlap Outer'              => 'Spec_Issue',
        'Berat Core Tidak Standar'   => 'Spec_Issue',
        'Low FC'                     => 'Spec_Issue',
        'Hasil Cetakan Berongga'     => 'Spec_Issue',

        // Moisture_Issue
        'Soft Core'  => 'Moisture_Issue',
        'MC High'    => 'Moisture_Issue',
        'Lembab'     => 'Moisture_Issue',

        // Thickness_Issue
        'T Variasi'  => 'Thickness_Issue',
        'T(-)'       => 'Thickness_Issue',

        // Rough_Cut
        'Potongan Tidak Rata'   => 'Rough_Cut',
        'Potongan Berserabut'   => 'Rough_Cut',

        // Printing_Defect
        'Tidak Ada Inner Printing'  => 'Printing_Defect',
        'Tidak Tersablon'           => 'Printing_Defect',
        'Salah Sablon'              => 'Printing_Defect',

        // Surface_Damage
        'Inner Tergerus'  => 'Surface_Damage',
    ];

    /**
     * Kamus sinonim: kata kunci lapangan (bahasa awam QA/customer) → Detail Ketidaksesuaian.
     * Key = kata kunci lowercase, Value = detail ketidaksesuaian kanonik.
     */
    public const SYNONYM_MAP = [
        // Length_Issue
        'panjang kurang'   => 'L(-)',
        'kurang panjang'   => 'L(-)',
        'ukuran pendek'    => 'L(-)',
        'pendek'           => 'L(-)',
        'l(-)'             => 'L(-)',
        'l(+)'             => 'L(+)',
        'kepanjangan'      => 'L(+)',
        'ukuran panjang'   => 'L(+)',
        'panjang'          => 'L Variasi',
        'length'           => 'L Variasi',

        // Visual_Defect
        'visual kotor'     => 'Visual Kotor',
        'kotor'            => 'Visual Kotor',
        'noda'             => 'Visual Kotor',
        'flek'             => 'Visual Kotor',
        'bercak'           => 'Visual Kotor',
        'visual'           => 'Visual Kotor',
        'ng visual'        => 'NG Visual',
        'berminyak'        => 'Inner Berminyak',
        'label sobek'      => 'Visual Inner Label Sobek',
        'sobek'            => 'Visual Inner Label Sobek',

        // Deformation
        'penyok'           => 'Dented',
        'gepeng'           => 'Dented',
        'bengkok'          => 'Bending',
        'melengkung'       => 'Bending',
        'bergelombang'     => 'Bergelombang',
        'dented'           => 'Dented',
        'oval'             => 'Oval',

        // ID_Issue
        'seret'            => 'Seret',
        'longgar'          => 'ID(+)',
        'kebesaran'        => 'ID(+)',
        'kekecilan'        => 'ID(-)',
        'sempit'           => 'ID(-)',
        'ketat'            => 'ID(-)',
        'diameter'         => 'ID Tidak Rata',
        'id'               => 'ID Tidak Rata',

        // Adhesion_Issue
        'peel off'         => 'Peel Off',
        'ngelupas'         => 'Peel Off',
        'mengelupas'       => 'Peel Off',
        'copot'            => 'Peel Off',
        'lepas'            => 'Peel Off',
        'lem'              => 'Lem Tidak Kuat',
        'peel'             => 'Peel Off',
        'unstuck'          => 'Unstuck Inner',

        // Spec_Issue
        'overlap'          => 'Overlap Outer',
        'berat'            => 'Berat Core Tidak Standar',
        'spec'             => 'Overlap Outer',
        'spek'             => 'Overlap Outer',
        'berongga'         => 'Hasil Cetakan Berongga',

        // Moisture_Issue
        'lembab'           => 'Lembab',
        'basah'            => 'Lembab',
        'soft'             => 'Soft Core',
        'moisture'         => 'MC High',

        // Thickness_Issue
        'tebal'            => 'T Variasi',
        'tipis'            => 'T(-)',
        'thickness'        => 'T Variasi',

        // Rough_Cut
        'serabut'          => 'Potongan Berserabut',
        'tidak rata'       => 'Potongan Tidak Rata',
        'potong'           => 'Potongan Tidak Rata',
        'kasar'            => 'Potongan Berserabut',

        // Printing_Defect
        'sablon'           => 'Tidak Tersablon',
        'print'            => 'Tidak Ada Inner Printing',
        'printing'         => 'Tidak Ada Inner Printing',
        'cetakan'          => 'Tidak Ada Inner Printing',

        // Surface_Damage
        'tergerus'         => 'Inner Tergerus',
        'goresan'          => 'Inner Tergerus',
        'baret'            => 'Inner Tergerus',
        'scratch'          => 'Inner Tergerus',
        'lecet'            => 'Inner Tergerus',
    ];

    /**
     * Kamus pemetaan: Penyebab Utama → Array Detail Penyebab (Berdasarkan data 200+ PT WBN).
     */
    public const PENYEBAB_TO_DETAIL_MAP = [
        'Handling' => [
            'Handling saat drying process',
            'Handling paper core',
            'Handling di customer',
            'Handling saat pengiriman',
            'Handling saat loading',
            'Handling saat finishing',
            'Handling produksi',
            'Handling saat seamless',
            'Tumpukan dalam proses drying',
            'Tumpukan berlebih',
            'Kumpulan dari handling',
        ],
        'Masalah Mesin' => [
            'Nilon pada mesin aus',
            'Kerusakan mesin printing',
            'Abrasi chuck spindle',
            'As mandrel perlu perbaikan',
            'As mengalami keausan',
        ],
        'Kondisi Penyimpanan' => [
            'Penyusutan akibat suhu gudang terlalu panas',
            'Suhu tinggi saat dalam truk',
            'Penyusutan akibat penyimpanan terlalu lama',
        ],
        'MC Tidak Stabil' => [
            'MC High',
            'Penyusutan akibat ketidakstabilan MC',
            'MC tidak stabil',
        ],
        'Material Tidak Standar' => [
            'Slit paper tidak standar',
            'Ketebalan paper tidak standar',
            'Lebar kraft tidak standar',
            'Paper yang digunakan terlalu tipis',
            'Material paper low PB',
            'Ketidaksesuaian spesifikasi As',
            'Kualitas lem tidak standar',
            'Menggunakan paper pinggiran',
            'Material core tidak standar',
            'Penyusutan akibat karakteristik CB Papertech',
        ],
        'Proses Drying Tidak Optimal' => [
            'Tanpa proses drying',
            'Over drying',
            'Proses drying tidak optimal',
        ],
        'Kegagalan Perekatan' => [
            'Delaminasi kraft',
            'Lem tidak merekat sempurna',
            'Sticking belum sempurna',
        ],
        'Material Ex-Stock' => [
            'Menggunakan ex-Trias bor',
            'Menggunakan ex-core',
            'Parent ex-Trigema terpotong',
            'Menggunakan ex-stock',
        ],
        'Settingan Tidak Tepat' => [
            'Pergeseran scraper pisau',
            'Setting tension tidak tepat',
            'Pelumas berlebih',
            'Setting winding tidak tepat',
            'Setting length lolos check',
        ],
        'Human Error' => [
            'Human error',
            'Tidak membaca WO',
            'Kesalahan internal',
            'Operator tidak melakukan cek',
            'Miss Komunikasi',
        ],
        'Masalah Pengukuran' => [
            'Salah pengukuran',
            'Perbedaan alat ukur MC',
        ],
        'Kesalahan Sambungan Core' => [
            'Core sambungan tercampur',
            'Sambungan tidak dipisahkan',
            'Menggunakan as sambungan',
        ],
        'Kualitas Cutting' => [
            'Potongan kualitas offsaw',
        ],
        'Dalam monitor' => [
            'Sesuai SOP',
        ],
    ];


    /**
     * Resolve kata kunci / detail ketidaksesuaian → ['jenis' => ..., 'detail' => ...].
     *
     * Urutan pencocokan:
     *   1. Exact match di DETAIL_TO_JENIS_MAP (case-insensitive)
     *   2. Partial match di SYNONYM_MAP (kata kunci terkandung dalam input)
     *
     * @return array{jenis: string|null, detail: string|null, matched_keyword: string|null}
     */
    public static function resolveFromKeyword(string $input): array
    {
        $clean = trim($input);
        if ($clean === '') {
            return ['jenis' => null, 'detail' => null, 'matched_keyword' => null];
        }

        $lower = mb_strtolower($clean);

        // 1. Exact match di DETAIL_TO_JENIS_MAP (case-insensitive)
        foreach (self::DETAIL_TO_JENIS_MAP as $detail => $jenis) {
            if (mb_strtolower($detail) === $lower) {
                return ['jenis' => $jenis, 'detail' => $detail, 'matched_keyword' => $detail];
            }
        }

        // 2. Cari sinonim terpanjang yang cocok (longest match first → lebih spesifik)
        $synonyms = self::SYNONYM_MAP;
        uksort($synonyms, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        foreach ($synonyms as $keyword => $detail) {
            if (mb_strpos($lower, $keyword) !== false) {
                $jenis = self::DETAIL_TO_JENIS_MAP[$detail] ?? null;
                return ['jenis' => $jenis, 'detail' => $detail, 'matched_keyword' => $keyword];
            }
        }

        return ['jenis' => null, 'detail' => null, 'matched_keyword' => null];
    }


    // ===================== Relationships =====================

    /** User / Staff QA yang menginput komplain. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
            $c->lead_time = static::hitungLeadTime($c);
        });

        static::updating(function (Complaint $c) {
            $c->lead_time = static::hitungLeadTime($c);
        });

        static::created(function (Complaint $c) {
            static::resequenceAll();
        });

        static::updated(function (Complaint $c) {
            if ($c->isDirty('tanggal_complain') || $c->isDirty('nama_customer')) {
                static::resequenceAll();
            }
        });

        static::deleted(function (Complaint $c) {
            static::resequenceAll();
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

    /**
     * Urutkan ulang (Re-sequence) seluruh nomor registrasi `no_customer` secara kronologis:
     * - Diurutkan berdasarkan `tanggal_complain` (terlama -> terbaru), lalu `id` ASC.
     * - Kode Customer 2-digit tetap konsisten per nama_customer.
     * - Nomor urut transaksi 3-digit diurutkan rapat (001, 002, 003, ...) tanpa ada angka bolong.
     */
    public static function resequenceAll(): void
    {
        $all = static::query()
            ->orderBy('tanggal_complain', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($all->isEmpty()) {
            return;
        }

        // Peta kode 2-digit per customer
        $codeByCustomer = [];
        $usedCodes = [];

        // 1. Kumpulkan kode customer yang sudah terpakai
        foreach ($all as $c) {
            $key = mb_strtolower(trim((string) $c->nama_customer));
            if ($key !== '' && !isset($codeByCustomer[$key])) {
                if (preg_match('/^(\d{2})-(\d{3})$/', (string) $c->no_customer, $m)) {
                    $code = $m[1];
                    if (!in_array($code, $usedCodes, true)) {
                        $codeByCustomer[$key] = $code;
                        $usedCodes[] = $code;
                    }
                }
            }
        }

        // 2. Jika ada customer baru yang belum punya kode
        $nextCodeInt = 1;
        foreach ($all as $c) {
            $key = mb_strtolower(trim((string) $c->nama_customer));
            if ($key !== '' && !isset($codeByCustomer[$key])) {
                while (in_array(str_pad((string) $nextCodeInt, 2, '0', STR_PAD_LEFT), $usedCodes, true)) {
                    $nextCodeInt++;
                }
                $newCode = str_pad((string) $nextCodeInt, 2, '0', STR_PAD_LEFT);
                $codeByCustomer[$key] = $newCode;
                $usedCodes[] = $newCode;
                $nextCodeInt++;
            }
        }

        // 3. Re-sequence transaksi (001, 002, 003, ...) berdasarkan urutan tanggal_complain asc
        $seq = 1;
        foreach ($all as $c) {
            $key = mb_strtolower(trim((string) $c->nama_customer));
            $custCode = $codeByCustomer[$key] ?? '01';
            $expectedNo = sprintf('%s-%03d', $custCode, $seq);

            if ($c->no_customer !== $expectedNo) {
                $c->no_customer = $expectedNo;
                $c->timestamps = false;
                $c->save();
                $c->timestamps = true;
            }

            $seq++;
        }
    }

    public static function recalculateTransactionNumbers(): void
    {
        static::resequenceAll();
    }

    protected static function hitungLeadTime(Complaint $c): ?int
    {
        if ($c->tanggal_complain && $c->tanggal_produksi) {
            $produksi = $c->tanggal_produksi instanceof Carbon ? $c->tanggal_produksi : Carbon::parse($c->tanggal_produksi);
            $complain = $c->tanggal_complain instanceof Carbon ? $c->tanggal_complain : Carbon::parse($c->tanggal_complain);
            return (int) abs($produksi->diffInDays($complain));
        }
        return null;
    }
}
