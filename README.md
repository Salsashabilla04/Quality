# Dashboard Monitoring Customer Complaint & NCR

Sistem monitoring Customer Complaint dan Non-Conformance Report (NCR) berbasis web
dengan pendekatan **Seven Tools Quality Control** dan **Algoritma Apriori**.
Studi kasus: PT Wahana Bermuda Nusantara.

Dibangun dengan **Laravel 11 + PHP 8.3 + MySQL**, grafik memakai **Chart.js**.

---

## 1. Menjalankan (pertama kali)

```bash
# a) Buat database & user MySQL (masukkan password root Anda saat diminta)
mysql -uroot -p < setup_mysql.sql

# b) Migrasi tabel + import data dari cleardata.xlsx (207 baris)
/usr/local/opt/php@8.3/bin/php artisan migrate:fresh --seed

# c) Jalankan server
./run.sh
# atau: /usr/local/opt/php@8.3/bin/php artisan serve
```

Buka **http://127.0.0.1:8000** → halaman **login**.

**Akun admin default:** `admin@wbn.test` / `admin123`

> Kredensial DB default (di `.env`): database `ncr_dashboard`, user `ncr_user`, password `ncr_pass`.

---

## 2. Halaman

| Menu | Isi |
|------|-----|
| **Dashboard** | KPI ringkas + Pareto + Peta Kendali + Top 5 Association Rules |
| **Seven Tools QC** | 7 alat: Check Sheet, Pareto, Histogram, Peta Kendali (c-chart), Scatter, Fishbone (6M), Stratifikasi |
| **Analisis Apriori** | Frequent itemsets & association rules (support/confidence/lift), parameter bisa diatur |
| **Data Complaint** | Tabel + filter, tambah/edit/hapus complaint |

Semua grafik & hasil Apriori **dihitung ulang otomatis** setiap data complaint berubah.

### Fitur tambahan
- **Login admin** — seluruh halaman terproteksi; hanya admin yang bisa lihat/input/edit/hapus.
- **Export** — tombol di tiap halaman:
  - Dashboard / Apriori → **Laporan PDF** (KPI, Pareto, Fishbone, frequent itemsets, rules, interpretasi)
  - Apriori → **Excel** (sheet Frequent Itemsets + Association Rules)
  - Data Complaint → **Export Excel** (seluruh data)
- **Rekomendasi tindakan otomatis** — di form input, saat memilih Jenis Ketidaksesuaian/Penyebab, sistem menyarankan Corrective & Preventive Action (dari histori kasus serupa) + menampilkan kaitan Apriori. Klik **"Pakai"** untuk mengisi otomatis.

---

## 3. Form Input Complaint — mana yang diisi vs otomatis

### 🔵 Diisi manual
| Field | Keterangan |
|-------|------------|
| Nama Customer | wajib (autocomplete dari data lama) |
| Tanggal Complain | wajib |
| Ukuran | mis. `3 x 14 x 924` |
| Qty | jumlah pcs NG |
| Area / Lokasi | mis. `T3`, `W2` |
| **Jenis Ketidaksesuaian** | daftar tersendiri — tiap entri = pilihan + Detail; tombol **"+ Tambah Ketidaksesuaian"** menambah entri (boleh banyak). Bisa **"➕ Tambah baru"**. Dipakai Apriori |
| **Penyebab** | daftar tersendiri & independen — tiap entri = pilihan + Detail; tombol **"+ Tambah Penyebab"**. Bisa **"➕ Tambah baru"**. Dipakai Apriori |
| Detail Penyebab | teks bebas |
| Corrective Action | tindakan koreksi |
| Preventive Action | tindakan pencegahan |
| Tanggal Produksi | opsional |
| Tanggal Kirim | opsional |
| Keterangan | Retur / Feedback / - |
| Status | Open / Close |

### Fishbone 6M per complaint
Di bagian bawah form ada **Diagram Sebab-Akibat (Fishbone 6M)** yang bisa diisi/diedit
(Man, Machine, Material, Method, Environment, Measurement — satu sebab per baris).
Tombol **"Isi otomatis dari Penyebab"** memetakan penyebab terpilih ke kategori 6M.

### Surat NCR (PDF per complaint)
Di halaman **Data Complaint**, klik ikon **⋮** pada baris → **Download PDF**. Muncul popup
untuk mengedit **Penyebab, Correction Action, Corrective Action, dan Fishbone 6M** khusus
untuk dokumen yang dicetak — **data asli tidak berubah**. PDF terdiri dari Hal.1 (form NCR)
dan Hal.2 (Lampiran Fishbone 6M), meniru format Surat NCR PT WBN.

### 🟢 Terisi otomatis
| Field | Cara terisi |
|-------|-------------|
| **No Customer** | `NN-NNN` → 2 digit kode customer (otomatis per nama) + 3 digit no transaksi |
| **Status** | default `Open` saat data baru |
| **Lead time** | dihitung: Tanggal Complain − Tanggal Produksi (hari) |
| created_at / updated_at | timestamp otomatis Laravel |

---

## 4. Algoritma Apriori

- 1 complaint = 1 transaksi. Karena ketidaksesuaian & penyebab kini **multi-value**, satu complaint bisa berisi banyak item (mis. `Ketidaksesuaian=Length_Issue`, `Ketidaksesuaian=Visual_Defect`, `Penyebab=Handling`). Frekuensi (support) menghitung tiap kemunculan, dan rule antar-cacat bisa muncul.
- Default: minimum support 5%, minimum confidence 50% (sama dengan `apriori_complain.py`).
- Output PHP **identik** dengan `hasil_apriori.xlsx` (tervalidasi).
- Implementasi: `app/Services/AprioriService.php`.

## 5. Seven Tools QC

Implementasi: `app/Services/SevenToolsService.php`. Mapping penyebab → kategori 6M
(Man, Machine, Material, Method, Environment, Measurement) ada di `app/Models/Complaint.php`.

---

## 6. Beralih sementara ke SQLite (jika MySQL bermasalah)

Ubah `.env`: `DB_CONNECTION=sqlite`, lalu:
```bash
touch database/database.sqlite
/usr/local/opt/php@8.3/bin/php artisan migrate:fresh --seed
```
