<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\DefectDictionary
 *
 * @property int $id
 * @property string $jenis_ketidaksesuaian
 * @property string|null $definisi
 * @property string|null $tindakan_rekomendasi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ComplaintItem> $complaintItems
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|DefectDictionary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DefectDictionary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DefectDictionary query()
 * @method static \Illuminate\Database\Eloquent\Builder|DefectDictionary where($column, $operator = null, $value = null)
 * @method static \Illuminate\Database\Eloquent\Collection|static[] all($columns = ['*'])
 * @method static static create(array $attributes = [])
 * @method static static firstOrCreate(array $attributes = [], array $values = [])
 * @method static static updateOrCreate(array $attributes, array $values = [])
 * @method static int count($columns = '*')
 */
class DefectDictionary extends Model
{
    protected $table = 'defect_dictionaries';

    protected $fillable = [
        'jenis_ketidaksesuaian',
        'definisi',
        'tindakan_rekomendasi',
    ];

    /**
     * Data taksonomi default kamus cacat PT WBN (seeder fallback)
     */
    public static function seedDefaultsIfEmpty(): void
    {
        $defaults = [
            [
                'jenis_ketidaksesuaian' => 'Length_Issue',
                'definisi' => 'Ukuran panjang roll atau lembaran material tidak sesuai dengan toleransi spesifikasi lembar order (terlalu pendek / berlebih).',
                'tindakan_rekomendasi' => 'Kalibrasi ulang sensor pencatat meter (counter) pada mesin rewinding/slitting dan verifikasi meter manual.',
            ],
            [
                'jenis_ketidaksesuaian' => 'Visual_Defect',
                'definisi' => 'Terdapat cacat tampilan fisik pada permukaan produk seperti bercak, bintik kotor, atau ketidaksesuaian visual.',
                'tindakan_rekomendasi' => 'Pembersihan ruang produksi (clean room filter) dan pemeriksaan kebersihan material sebelum proses.',
            ],
            [
                'jenis_ketidaksesuaian' => 'Deformation',
                'definisi' => 'Bentuk fisik roll menggelembung, melengkung (wavy), penyok, atau tidak presisi akibat tekanan tension yang tidak seimbang.',
                'tindakan_rekomendasi' => 'Atur ulang tension roll pada mesin rewinding & pastikan tumpukan palet di gudang tidak terlalu tinggi.',
            ],
            [
                'jenis_ketidaksesuaian' => 'ID_Issue',
                'definisi' => 'Diameter bagian dalam (Inner Diameter / Core) tidak pas dengan chuck mesin customer (longgar atau terlalu sempit).',
                'tindakan_rekomendasi' => 'Ukur diameter dalam core mengunakan Vernier Caliper terkalibrasi sebelum proses penggulungan.',
            ],
            [
                'jenis_ketidaksesuaian' => 'Adhesion_Issue',
                'definisi' => 'Daya rekat perekat (adhesive) lemah, melar, mudah terkelupas (delaminasi), atau tidak menempel sempurna.',
                'tindakan_rekomendasi' => 'Cek suhu oven dryer, rasio formula lem, dan ketebalan aplikasi adhesive secara berkala.',
            ],
            [
                'jenis_ketidaksesuaian' => 'Spec_Issue',
                'definisi' => 'Spesifikasi teknis material (gramatur, tipe film, atau tensile strength) tidak sesuai dengan Purchasing Order (PO).',
                'tindakan_rekomendasi' => 'Lakukan verifikasi Mill Test Certificate (MTC) dari supplier sebelum material diproses.',
            ],
            [
                'jenis_ketidaksesuaian' => 'Moisture_Issue',
                'definisi' => 'Kadar air (Moisture Content) pada bahan melampaui standar toleransi maksimum sehingga berpotensi lembap.',
                'tindakan_rekomendasi' => 'Pastikan humidity ruangan gudang terjaga (<60% RH) dan suhu oven pembakaran terkontrol.',
            ],
            [
                'jenis_ketidaksesuaian' => 'Thickness_Issue',
                'definisi' => 'Ketebalan lembaran produk tidak seragam (profil thickness uneven) antar bagian kiri, tengah, dan kanan.',
                'tindakan_rekomendasi' => 'Stel ulang celah die lip mesin extruder & bersihkan lip die dari kerak material.',
            ],
            [
                'jenis_ketidaksesuaian' => 'Rough_Cut',
                'definisi' => 'Hasil pemotongan pinggiran (cutting edge / trimming) kasar, berserabut, atau robek kecil.',
                'tindakan_rekomendasi' => 'Ganti pisau slitter yang tumpul dan atur sudut potong pisau potong (slitting blade).',
            ],
            [
                'jenis_ketidaksesuaian' => 'Printing_Defect',
                'definisi' => 'Hasil cetakan grafik/teks tidak presisi (miss-register), warna pudar, atau tidak tersablon.',
                'tindakan_rekomendasi' => 'Lakukan registrasi silinder cetak & periksa viskositas tinta cetak secara periodik.',
            ],
            [
                'jenis_ketidaksesuaian' => 'Surface_Damage',
                'definisi' => 'Kerusakan fisik bagian luar roll seperti terbentur, sobek luar, atau tergerus saat proses handling & kirim.',
                'tindakan_rekomendasi' => 'Gunakan bantal pelindung (corner protector) dan lapisi bungkus bubble wrap tebal pada tiap roll.',
            ],
        ];

        foreach ($defaults as $row) {
            static::firstOrCreate(
                ['jenis_ketidaksesuaian' => $row['jenis_ketidaksesuaian']],
                [
                    'definisi'             => $row['definisi'],
                    'tindakan_rekomendasi' => $row['tindakan_rekomendasi'],
                ]
            );
        }
    }

    /**
     * Relasi ke riwayat complaint items
     */
    public function complaintItems(): HasMany
    {
        return $this->hasMany(ComplaintItem::class, 'jenis_ketidaksesuaian', 'jenis_ketidaksesuaian');
    }
}

