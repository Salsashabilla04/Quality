# Analisis Fitur Sistem & Cara Kerja Algoritma Apriori

Sistem **Salsa-Arpriory (Customer Complaint & NCR)** dirancang sebagai pusat manajemen kualitas (Quality Assurance) yang komprehensif. Sistem ini tidak hanya mencatat data keluhan, namun juga mengolahnya menjadi wawasan analitik secara *real-time*.

Berikut adalah analisis fitur-fitur utama di dalam sistem ini, dengan sorotan khusus pada bagaimana algoritma **Apriori** bekerja.

---

## 1. Fitur-fitur Utama Sistem

### A. Otomatisasi & Integritas Data (Data Management)
- **Import/Export Pintar:** Sistem dapat membaca data Excel secara mentah dan melakukan *typo-normalization* (memperbaiki salah ketik) agar kategori cacat dan penyebab selalu konsisten. 
- **Penomoran Kronologis Otomatis:** Sistem memiliki kecerdasan untuk mendaftarkan Master Customer dan mengurutkan ID transaksi (Nomor Complaint) secara kronologis dari waktu ke waktu, terlepas dari kapan QA memasukkan datanya.

### B. Seven Tools of Quality (Dashboard Analitik)
Sistem ini menggunakan `SevenToolsService` untuk menyediakan berbagai diagram alat kendali mutu (QC) secara langsung (*live*), meliputi:
- **KPI Metrics:** Melacak total keluhan, status open/close, *lead time* penyelesaian, dan cacat/penyebab paling dominan.
- **Diagram Pareto:** Mengidentifikasi cacat penyumbang 80% masalah terbesar. Hebatnya, sistem bisa memfilter Pareto secara umum maupun spesifik per pelanggan.
- **Trend & Stratifikasi:** Menampilkan tren keluhan dari bulan ke bulan atau tahun ke tahun.

### C. Pembuatan Laporan NCR & Diagram Fishbone (6M)
Sistem secara otomatis mengkategorikan akar masalah (Penyebab) ke dalam kerangka **Fishbone 6M** *(Man, Machine, Material, Method, Environment, Measurement)*. Sistem juga menyediakan pop-up interaktif bagi QA untuk menyesuaikan isi Fishbone sebelum mencetaknya ke dalam bentuk dokumen NCR (PDF).

---

## 2. Sorotan Utama: Algoritma Apriori (Association Rules)

Fitur paling cerdas di dalam sistem ini adalah **AprioriService**, yang dibangun secara murni (*native*) menggunakan PHP. Fitur ini bertugas melakukan *Data Mining* untuk menemukan **pola keterkaitan** antara "Jenis Cacat" (Ketidaksesuaian) dan "Akar Masalah" (Penyebab).

> [!TIP]
> **Tujuan Apriori:** Menemukan aturan seperti *"Jika terjadi cacat A, maka kemungkinan besar penyebabnya adalah B"*, sehingga perusahaan bisa melakukan tindakan preventif yang lebih akurat di masa depan.

### Bagaimana Apriori Bekerja di Sistem Ini?

#### Langkah 1: Pembentukan Keranjang Belanja (Basket / Transaksi)
Algoritma Apriori umumnya digunakan di supermarket (analisis keranjang belanja). Di sistem ini, konsep tersebut diadaptasi:
- **1 Transaksi = 1 Data Complaint.**
- **Item dalam Keranjang = Daftar Ketidaksesuaian & Daftar Penyebab.**
- *Contoh:* Jika pada satu keluhan terdapat cacat "Visual Defect" karena "Handling", maka sistem menyatukannya dalam satu keranjang: `[Ketidaksesuaian=Visual Defect, Penyebab=Handling]`.

#### Langkah 2: Pencarian Pola yang Sering Muncul (*Frequent Itemsets*)
Sistem menetapkan nilai batas minimum (**Min Support** = 5%).
- Sistem menghitung kombinasi item apa saja yang muncul bersamaan lebih dari batas minimal tersebut.
- Proses pencarian dilakukan secara bertingkat *(level by level)*. Mulai dari melihat kombinasi 1 item, kemudian 2 item, 3 item, dst. Kombinasi yang jarang muncul akan langsung dipangkas *(pruning)* untuk mempercepat komputasi.

#### Langkah 3: Ekstraksi Aturan (*Association Rules*)
Dari kombinasi yang sering muncul, sistem mencari **aturan sebab-akibat** yang kuat dengan batas **Min Confidence** (contoh: 50%).
Misalnya, dari semua kasus *"Visual Defect"*, seberapa sering *"Handling"* ikut muncul? Jika kemungkinannya di atas 50%, sistem akan membentuk aturan:
`Visual Defect ➔ Handling`

#### Langkah 4: Perhitungan Kekuatan Hubungan (*Lift Ratio*)
Untuk memastikan bahwa hubungan tersebut bukan karena kebetulan semata, sistem menghitung parameter **Lift**.
Sistem membaginya ke dalam 4 kategori kekuatan:
- **Sangat Kuat:** Lift ≥ 5
- **Kuat:** Lift ≥ 3
- **Sedang:** Lift ≥ 1.5
- **Lemah:** Lift < 1.5

#### Langkah 5: Terjemahan ke Bahasa Manusia (*Human-Readable Interpretation*)
Fitur yang membuat sistem ini sangat ramah pengguna adalah kemampuannya "berbicara". Daripada hanya menampilkan angka matematis, algoritma mengubah rumusnya menjadi kalimat interpretasi.
Contoh hasil output di Dashboard:
> *"Dari semua kasus **Visual Defect**, sebanyak **85%** terkait dengan **Handling**. Kaitan keduanya **Sangat Kuat** (lift 5.2x)."*

### Keunggulan Eksekusi Apriori di Sistem Ini
1. **Tanpa Library Eksternal:** Karena ditulis murni dalam PHP, sistem tidak bergantung pada *backend* Python atau library machine learning eksternal, sehingga server menjadi lebih ringan dan tidak butuh konfigurasi *API* khusus.
2. **Real-time:** Algoritma membaca langsung tabel `complaint_items` pada saat halaman dimuat. Jika Anda mengimpor 10 data keluhan baru di Excel, nilai *confidence* dan *lift* pada aturan Apriori di Dashboard akan langsung berubah detik itu juga menyesuaikan probabilitas terbaru.
