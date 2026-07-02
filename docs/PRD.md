# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# MAINTIFY (Branding: SERVTRACK)
### Platform Digital Pengelolaan Histori Service Kendaraan Bermotor Berbasis QR Code

---

**Versi Dokumen:** 1.0
**Tanggal Penyusunan:** 02 Juli 2026
**Status:** Draft Final — Siap untuk Development Reference
**Disusun oleh:** Product Management Team

---

<div style="page-break-after: always;"></div>

## 1. COVER

| | |
|---|---|
| **Nama Produk** | Maintify |
| **Nama Brand / Marketing** | Maintify |
| **Tagline** | "Satu Scan, Seluruh Riwayat Kendaraan Anda" |
| **Jenis Dokumen** | Product Requirements Document (PRD) |
| **Platform** | Website (Web Application, Responsive) |
| **Versi Dokumen** | 1.0 |
| **Tanggal Rilis Dokumen** | 02 Juli 2026 |
| **Klasifikasi** | Internal — Confidential |
| **Pemilik Dokumen** | Product Manager, Maintify |

---

## 2. INFORMASI DOKUMEN

### 2.1 Riwayat Revisi Dokumen

| Versi | Tanggal | Penulis | Deskripsi Perubahan |
|---|---|---|---|
| 0.1 | 2026-06-10 | Product Manager | Draft awal berdasarkan brief produk |
| 0.5 | 2026-06-20 | Product Manager & UX Analyst | Penambahan user persona, journey, dan flow |
| 1.0 | 2026-07-02 | Product Manager | Finalisasi PRD lengkap untuk seluruh tim development |

### 2.2 Daftar Distribusi

| Peran | Nama Tim | Tujuan Akses |
|---|---|---|
| Product Owner | Product Team | Validasi scope dan prioritas |
| UI/UX Designer | Design Team | Acuan desain wireframe & hi-fi |
| Frontend Developer | Engineering Team | Acuan pengembangan antarmuka |
| Backend Developer | Engineering Team | Acuan API dan struktur data |
| QA Engineer | Quality Assurance Team | Acuan pembuatan test case |
| DevOps Engineer | Infrastructure Team | Acuan deployment dan infrastruktur |

### 2.3 Referensi Dokumen Terkait

- Brief Produk Maintify (Internal)
- Studi Implementasi QR Code Program Subsidi BBM Pertamina (Referensi Konsep)
- Standar Desain UI/UX Internal Perusahaan

### 2.4 Glosarium

| Istilah | Definisi |
|---|---|
| Maintify | Nama produk/sistem |
| SERVTRACK | Nama brand yang digunakan pada antarmuka pengguna |
| QR Code Kendaraan | Kode QR unik yang merepresentasikan identitas digital sebuah kendaraan |
| Bengkel Mitra | Bengkel yang telah terverifikasi dan terdaftar dalam sistem Maintify |
| VIN | Vehicle Identification Number, nomor identifikasi unik kendaraan |
| Odometer | Alat pengukur jarak tempuh kendaraan |
| Digital ID | Identitas digital kendaraan berbasis QR Code |
| Transfer Kepemilikan | Proses pemindahan hak akses data kendaraan dari pemilik lama ke pemilik baru |
| Super Admin | Administrator tertinggi yang mengelola seluruh sistem Maintify |
| Admin Bengkel | Administrator yang mengelola operasional satu bengkel mitra |
| Pegawai Bengkel | Staf operasional bengkel yang menginput data service |
| OTP | One Time Password, kode verifikasi sekali pakai |
| SLA | Service Level Agreement |
| MVP | Minimum Viable Product |


---

## 3. EXECUTIVE SUMMARY

Maintify (dengan brand komersial **SERVTRACK**) adalah sebuah platform website yang dirancang untuk mendigitalisasi pengelolaan histori service kendaraan bermotor menggunakan teknologi QR Code sebagai identitas digital kendaraan. Setiap kendaraan yang terdaftar dalam sistem akan memiliki QR Code unik yang ditempelkan secara fisik pada kendaraan tersebut. QR Code ini menjadi jembatan antara dunia fisik (kendaraan dan bengkel) dengan dunia digital (data histori service), sehingga setiap aktivitas perawatan kendaraan dapat tercatat secara real-time, akurat, dan dapat diakses kapan saja oleh pemilik kendaraan.

Produk ini lahir dari kebutuhan pasar akan sistem pencatatan service kendaraan yang selama ini masih dilakukan secara manual — baik melalui buku servis fisik yang mudah hilang atau rusak, maupun melalui pencatatan yang tersebar di berbagai bengkel berbeda tanpa adanya satu sumber data yang terpadu. Inspirasi implementasi QR Code sebagai identitas terverifikasi merujuk pada keberhasilan program subsidi BBM Pertamina yang menggunakan QR Code sebagai alat validasi kendaraan penerima subsidi.

Maintify melayani empat kelompok pengguna utama: **Pelanggan** (pemilik kendaraan), **Pegawai Bengkel** (operator lapangan yang mencatat service), **Admin Bengkel** (pengelola operasional bengkel mitra), dan **Super Admin** (pengelola platform secara keseluruhan). Setiap peran memiliki hak akses dan alur kerja yang berbeda, namun saling terintegrasi dalam satu ekosistem data yang konsisten.

Fitur inti dari Maintify mencakup: pendaftaran dan pengelolaan kendaraan, pembuatan dan pemindaian QR Code, pencatatan riwayat service secara detail (oli, sparepart, odometer, kondisi kendaraan), pencarian bengkel mitra terdekat berbasis peta, sistem pengingat service, dashboard monitoring kesehatan kendaraan, serta fitur transfer kepemilikan kendaraan yang memungkinkan riwayat service tetap utuh meskipun kendaraan berpindah tangan.

Dokumen ini disusun untuk menjadi acuan tunggal (single source of truth) bagi seluruh tim pengembangan — mulai dari UI/UX Designer, Frontend Developer, Backend Developer, Quality Assurance, hingga DevOps Engineer — dalam membangun dan meluncurkan Maintify sebagai produk yang matang, aman, dan siap digunakan oleh publik.

---

## 4. LATAR BELAKANG

Industri otomotif Indonesia terus berkembang pesat, ditandai dengan meningkatnya jumlah kendaraan bermotor yang beroperasi setiap tahunnya. Pertumbuhan ini tidak diimbangi dengan sistem pencatatan riwayat perawatan kendaraan yang terstandarisasi. Sebagian besar pemilik kendaraan masih mengandalkan buku servis fisik yang diberikan oleh bengkel resmi, yang memiliki berbagai kelemahan signifikan:

- Buku servis fisik rentan hilang, rusak, atau tertinggal.
- Data service tidak terintegrasi apabila kendaraan diservis di bengkel yang berbeda-beda.
- Tidak ada mekanisme verifikasi independen atas riwayat yang tercatat.
- Sulit dilacak ketika kendaraan berpindah kepemilikan (jual-beli kendaraan bekas), sehingga pembeli tidak memiliki gambaran utuh mengenai kondisi kendaraan.
- Tidak ada pengingat otomatis untuk jadwal service berikutnya.

Di sisi lain, teknologi QR Code telah terbukti efektif sebagai alat identifikasi dan validasi digital dalam skala nasional, sebagaimana diterapkan pada program subsidi BBM bersubsidi oleh Pertamina, di mana setiap kendaraan terdaftar memiliki QR Code sebagai identitas resmi untuk validasi transaksi.

Berangkat dari observasi tersebut, Maintify dikembangkan sebagai solusi digital yang mengadaptasi konsep identitas kendaraan berbasis QR Code, namun difokuskan pada pengelolaan riwayat perawatan dan kesehatan kendaraan. Setiap kendaraan yang terdaftar di Maintify akan memiliki QR Code fisik yang ditempelkan pada bodi kendaraan. Ketika kendaraan dibawa ke bengkel mitra, pegawai bengkel cukup memindai QR Code tersebut untuk langsung mengakses dan memperbarui data histori service kendaraan yang bersangkutan.

Dengan pendekatan ini, Maintify berupaya menjadi *single source of truth* bagi riwayat perawatan kendaraan bermotor di Indonesia — memberikan manfaat baik bagi pemilik kendaraan (transparansi dan kemudahan monitoring), bengkel mitra (efisiensi pencatatan dan basis pelanggan yang lebih terstruktur), maupun bagi ekosistem jual-beli kendaraan bekas (peningkatan kepercayaan melalui riwayat service yang terverifikasi).

---

## 5. PROBLEM STATEMENT

| No | Permasalahan | Dampak |
|---|---|---|
| 1 | Riwayat service kendaraan tercatat secara manual dan tersebar di berbagai bengkel | Data tidak terpusat, sulit dilacak, rawan hilang |
| 2 | Tidak ada sistem verifikasi independen terhadap riwayat service | Rentan manipulasi data odometer dan riwayat service saat jual-beli kendaraan bekas |
| 3 | Pemilik kendaraan tidak memiliki visibilitas real-time terhadap kondisi kendaraannya | Keterlambatan penanganan masalah kendaraan, risiko keselamatan meningkat |
| 4 | Tidak ada pengingat otomatis jadwal service berkala | Kendaraan berisiko mengalami kerusakan akibat keterlambatan perawatan |
| 5 | Proses pencarian bengkel terpercaya dilakukan secara konvensional (word of mouth) | Waktu dan usaha yang tidak efisien bagi pemilik kendaraan |
| 6 | Riwayat kendaraan sulit diwariskan/dipindahkan ketika kendaraan berpindah kepemilikan | Nilai jual kendaraan bekas sulit terverifikasi, potensi penipuan riwayat kendaraan |
| 7 | Bengkel tidak memiliki sistem pencatatan digital yang efisien dan terstandarisasi | Proses administrasi manual memakan waktu, human error tinggi |

---

## 6. SOLUSI

Maintify menghadirkan solusi berupa platform digital terintegrasi dengan pendekatan berikut:

1. **Identitas Digital Kendaraan berbasis QR Code** — setiap kendaraan memiliki QR Code unik yang menjadi kunci akses ke seluruh riwayat data kendaraan tersebut.
2. **Pencatatan Real-Time oleh Bengkel Mitra** — pegawai bengkel memindai QR Code dan langsung mencatatkan hasil service ke dalam sistem, meniadakan pencatatan manual berbasis kertas.
3. **Dashboard Monitoring Kendaraan** — pemilik kendaraan dapat memantau status kesehatan kendaraan, sisa umur oli, dan jarak tempuh secara real-time.
4. **Sistem Pengingat Service** — notifikasi otomatis untuk jadwal service berikutnya berdasarkan waktu maupun jarak tempuh.
5. **Direktori Bengkel Mitra Terverifikasi** — fitur pencarian bengkel terdekat lengkap dengan status verifikasi, rating, dan navigasi peta.
6. **Transfer Kepemilikan Digital** — memungkinkan riwayat kendaraan berpindah secara sah dan terverifikasi mengikuti perpindahan kepemilikan kendaraan, meningkatkan kepercayaan pada transaksi jual-beli kendaraan bekas.
7. **Multi-Role Access Control** — sistem berlapis yang memisahkan hak akses Pelanggan, Pegawai Bengkel, Admin Bengkel, dan Super Admin agar tata kelola data tetap aman dan terstruktur.


---

## 7. TUJUAN PRODUK

### 7.1 Tujuan Bisnis

- Menjadi platform rujukan utama pengelolaan riwayat service kendaraan bermotor berbasis digital di Indonesia.
- Membangun jaringan bengkel mitra terverifikasi yang luas dan terpercaya.
- Menciptakan model data riwayat kendaraan yang dapat dimonetisasi di masa depan (misalnya kerjasama dengan platform jual-beli kendaraan bekas atau asuransi kendaraan).
- Meningkatkan loyalitas pelanggan bengkel mitra melalui sistem pengingat dan basis data pelanggan yang terstruktur.

### 7.2 Tujuan Produk

- Menyediakan satu sumber data terpusat dan terverifikasi untuk riwayat service kendaraan.
- Mempermudah pemilik kendaraan memantau kondisi kendaraannya kapan saja dan di mana saja.
- Mempercepat dan mendigitalisasi proses pencatatan service di bengkel mitra.
- Memfasilitasi transfer kepemilikan kendaraan dengan riwayat service yang tetap utuh dan terverifikasi.
- Menyediakan sistem pengingat service otomatis untuk mengurangi risiko keterlambatan perawatan kendaraan.

### 7.3 Tujuan Pengguna

| Pengguna | Tujuan |
|---|---|
| Pelanggan | Memantau kondisi kendaraan, riwayat service, dan mendapatkan pengingat service secara mudah |
| Pegawai Bengkel | Mencatat hasil service dengan cepat dan akurat melalui scan QR Code |
| Admin Bengkel | Mengelola operasional bengkel, pegawai, dan pelanggan secara efisien |
| Super Admin | Mengawasi dan mengendalikan seluruh ekosistem platform secara terpusat |

---

## 8. SCOPE PROJECT

### 8.1 In Scope (Termasuk dalam Pengembangan Tahap Ini)

- Website responsif (desktop dan mobile web) untuk keempat role pengguna.
- Sistem autentikasi (login, register, forgot password, OTP untuk Super Admin).
- Modul pengelolaan kendaraan (tambah, edit, lihat detail, hapus).
- Generator dan pemindai QR Code kendaraan.
- Modul pencatatan dan riwayat service.
- Modul pencarian bengkel mitra berbasis peta/lokasi.
- Modul transfer kepemilikan kendaraan.
- Dashboard untuk masing-masing role (Pelanggan, Admin Bengkel, Super Admin).
- Modul verifikasi bengkel mitra oleh Super Admin.
- Sistem notifikasi/pengingat service.
- Modul laporan sederhana untuk Admin Bengkel.
- Audit log untuk Super Admin.

### 8.2 Out of Scope (Tidak Termasuk dalam Tahap Ini)

- Aplikasi native mobile (iOS/Android) — direncanakan sebagai pengembangan lanjutan.
- Integrasi pembayaran/e-commerce sparepart.
- Fitur marketplace jual-beli kendaraan bekas.
- Integrasi langsung dengan sistem OBD (On-Board Diagnostics) kendaraan.
- Modul asuransi kendaraan terintegrasi.
- Multi-bahasa penuh (tahap awal hanya Bahasa Indonesia, dengan penyediaan tombol bahasa untuk pengembangan mendatang).

### 8.3 Batasan Proyek

- Pengembangan dilakukan dalam bentuk MVP (Minimum Viable Product) terlebih dahulu sebelum penambahan fitur lanjutan.
- QR Code fisik (stiker) dicetak dan didistribusikan melalui proses operasional terpisah di luar sistem website (proses bisnis, bukan bagian dari pengembangan software).
- Verifikasi bengkel mitra bersifat manual oleh Super Admin pada tahap awal.

---

## 9. STAKEHOLDER

| Stakeholder | Peran | Kepentingan Utama |
|---|---|---|
| Founder / Business Owner | Pengambil keputusan bisnis | Keberhasilan produk di pasar, ROI |
| Product Manager | Pemilik produk dan dokumen PRD | Kejelasan requirement dan prioritas fitur |
| UI/UX Designer | Perancang antarmuka dan pengalaman pengguna | Desain yang intuitif dan sesuai kebutuhan user |
| Frontend Developer | Pengembang antarmuka pengguna | Implementasi UI yang sesuai spesifikasi |
| Backend Developer | Pengembang sistem dan API | Reliabilitas sistem, integritas data |
| QA Engineer | Penjamin kualitas produk | Produk bebas bug, sesuai acceptance criteria |
| DevOps Engineer | Pengelola infrastruktur dan deployment | Sistem stabil, aman, dan scalable |
| Bengkel Mitra | Pengguna eksternal (Admin & Pegawai Bengkel) | Kemudahan operasional pencatatan service |
| Pelanggan (Pemilik Kendaraan) | Pengguna eksternal utama | Kemudahan monitoring kendaraan |
| Super Admin Internal | Pengelola platform | Kontrol penuh atas ekosistem dan kualitas data |

---

## 10. TARGET USER

### 10.1 Segmentasi Pengguna

| Role | Deskripsi | Perangkat Utama |
|---|---|---|
| Pelanggan | Pemilik kendaraan bermotor (motor/mobil) yang ingin memantau riwayat dan kondisi kendaraannya | Smartphone (web mobile) |
| Pegawai Bengkel | Staf teknis bengkel mitra yang bertugas melakukan service dan mencatat hasil pekerjaan | Smartphone/Tablet bengkel |
| Admin Bengkel | Pemilik/pengelola bengkel mitra yang mengawasi operasional bengkel | Desktop/Laptop |
| Super Admin | Tim internal Maintify yang mengelola seluruh platform | Desktop/Laptop |

### 10.2 Karakteristik Demografis Pengguna Utama (Pelanggan)

- Usia: 20–50 tahun
- Kepemilikan kendaraan bermotor pribadi (motor dan/atau mobil)
- Melek teknologi digital dan terbiasa menggunakan aplikasi berbasis smartphone
- Berdomisili di wilayah urban maupun sub-urban dengan akses internet memadai


---

## 11. USER PERSONA

### Persona 1: Pelanggan — "Rian, Si Pekerja Mobile"

| Atribut | Detail |
|---|---|
| Nama | Rian Pratama |
| Usia | 29 tahun |
| Pekerjaan | Sales Executive |
| Kendaraan | 1 unit motor matic untuk mobilitas harian |
| Perilaku | Sering bepergian jauh untuk bertemu klien, sangat bergantung pada kondisi motornya tetap prima |
| Kebutuhan | Pengingat service otomatis, kemudahan mencari bengkel terdekat saat berada di luar kota |
| Frustrasi | Sering lupa jadwal ganti oli, buku servis fisik sering tertinggal atau hilang |
| Tujuan Menggunakan Maintify | Memastikan motor selalu dalam kondisi optimal tanpa harus mengingat manual jadwal service |

### Persona 2: Pelanggan — "Dewi, Ibu Rumah Tangga Modern"

| Atribut | Detail |
|---|---|
| Nama | Dewi Anggraini |
| Usia | 38 tahun |
| Pekerjaan | Ibu rumah tangga, mengelola kendaraan keluarga |
| Kendaraan | 1 unit mobil keluarga |
| Perilaku | Bertanggung jawab mengurus jadwal service mobil keluarga di sela kesibukan rumah tangga |
| Kebutuhan | Dashboard yang mudah dipahami, riwayat service yang jelas untuk didiskusikan dengan suami |
| Frustrasi | Tidak familiar dengan istilah teknis otomotif, butuh tampilan yang sederhana |
| Tujuan Menggunakan Maintify | Memantau kesehatan kendaraan keluarga tanpa perlu pemahaman teknis mendalam |

### Persona 3: Pegawai Bengkel — "Bayu, Mekanik Berpengalaman"

| Atribut | Detail |
|---|---|
| Nama | Bayu Setiawan |
| Usia | 26 tahun |
| Pekerjaan | Mekanik di bengkel mitra Maintify |
| Perilaku | Menangani belasan kendaraan per hari, membutuhkan proses pencatatan yang cepat |
| Kebutuhan | Interface scan QR yang cepat, form input service yang ringkas dan efisien |
| Frustrasi | Pencatatan manual di buku servis memakan waktu dan rawan kesalahan tulis |
| Tujuan Menggunakan Maintify | Mempercepat proses administrasi service agar bisa fokus pada pekerjaan teknis |

### Persona 4: Admin Bengkel — "Pak Hendra, Pemilik Bengkel"

| Atribut | Detail |
|---|---|
| Nama | Hendra Wijaya |
| Usia | 45 tahun |
| Pekerjaan | Pemilik bengkel mitra dengan 5 pegawai |
| Perilaku | Mengelola operasional bengkel, memantau kinerja pegawai dan kepuasan pelanggan |
| Kebutuhan | Laporan operasional, data pelanggan terstruktur, kemudahan mengelola pegawai |
| Frustrasi | Sulit memantau performa bengkel tanpa sistem digital yang terpusat |
| Tujuan Menggunakan Maintify | Meningkatkan efisiensi operasional dan loyalitas pelanggan bengkelnya |

### Persona 5: Super Admin — "Tim Internal Maintify"

| Atribut | Detail |
|---|---|
| Nama | Sarah (Operations Team Maintify) |
| Usia | 31 tahun |
| Pekerjaan | Platform Operations Manager |
| Perilaku | Mengawasi kualitas data, memverifikasi bengkel baru, memantau kesehatan sistem |
| Kebutuhan | Dashboard monitoring sistem yang komprehensif, audit log yang lengkap |
| Frustrasi | Risiko bengkel fiktif mendaftar tanpa proses verifikasi yang ketat |
| Tujuan Menggunakan Maintify | Menjaga integritas dan kualitas ekosistem platform secara keseluruhan |

---

## 12. USER JOURNEY

### 12.1 User Journey — Pelanggan (Onboarding hingga Service Pertama)

| Tahap | Aktivitas | Emosi | Touchpoint |
|---|---|---|---|
| Awareness | Mengetahui Maintify dari bengkel langganan atau media sosial | Penasaran | Landing page, promosi bengkel |
| Registrasi | Mendaftar akun dan menambahkan data kendaraan | Antusias, sedikit hati-hati | Halaman Register, Tambah Kendaraan |
| Aktivasi | Mendapatkan QR Code kendaraan dan menempelkannya di kendaraan | Puas | Halaman QR Code Kendaraan |
| Penggunaan Awal | Membawa kendaraan ke bengkel mitra, pegawai bengkel scan QR Code | Percaya | Proses di bengkel |
| Monitoring | Melihat riwayat service dan status kesehatan kendaraan di dashboard | Nyaman, terinformasi | Dashboard Beranda, Detail Kendaraan |
| Retensi | Menerima pengingat service berikutnya | Terbantu | Notifikasi sistem |
| Loyalitas | Menggunakan fitur pencarian bengkel terdekat saat bepergian | Terlayani | Halaman Bengkel Terdekat |

### 12.2 User Journey — Pegawai Bengkel (Proses Service Kendaraan)

| Tahap | Aktivitas | Emosi | Touchpoint |
|---|---|---|---|
| Login | Masuk ke sistem menggunakan akun bengkel | Rutin | Halaman Login |
| Identifikasi | Scan QR Code kendaraan pelanggan | Cepat, efisien | Fitur Scan QR |
| Verifikasi Data | Melihat data kendaraan dan riwayat sebelumnya | Terinformasi | Halaman Detail Kendaraan |
| Input Service | Menambahkan data hasil service baru | Fokus | Form Tambah Riwayat Service |
| Konfirmasi | Menyimpan data dan memberi tahu pelanggan | Puas | Konfirmasi sistem |

### 12.3 User Journey — Transfer Kepemilikan Kendaraan

| Tahap | Aktivitas | Emosi |
|---|---|---|
| Inisiasi | Pemilik lama membuka menu Transfer Kepemilikan | Hati-hati |
| Input Data | Mengisi data penerima (pembeli) | Waspada |
| Verifikasi | Sistem memverifikasi data penerima | Menunggu |
| Review | Kedua pihak meninjau data sebelum transfer final | Teliti |
| Konfirmasi | Transfer disetujui, kepemilikan berpindah | Lega |
| Selesai | Halaman Transfer Berhasil ditampilkan | Puas |


---

## 13. BUSINESS PROCESS

### 13.1 Proses Bisnis Utama — Pendaftaran Kendaraan dan Penerbitan QR Code

1. Pelanggan mendaftar akun di Maintify.
2. Pelanggan menambahkan data kendaraan (merek, tipe, tahun, VIN, plat nomor, dsb).
3. Sistem men-generate QR Code unik untuk kendaraan tersebut.
4. Pelanggan mengunduh/mencetak QR Code dan menempelkannya secara fisik pada kendaraan (proses operasional di luar sistem, dapat dibantu oleh bengkel mitra atau dicetak mandiri).
5. QR Code aktif dan siap dipindai oleh bengkel mitra mana pun yang terverifikasi dalam sistem.

### 13.2 Proses Bisnis — Pencatatan Service oleh Bengkel Mitra

1. Pelanggan datang ke bengkel mitra terverifikasi.
2. Pegawai bengkel login ke sistem menggunakan akun bengkel.
3. Pegawai bengkel memindai QR Code kendaraan pelanggan.
4. Sistem menampilkan data kendaraan dan riwayat service sebelumnya.
5. Pegawai bengkel melakukan service dan menginput data hasil service (jenis service, sparepart, oli, odometer, kondisi kendaraan, catatan mekanik).
6. Data tersimpan otomatis ke akun pelanggan dan dapat langsung dilihat oleh pelanggan melalui dashboard mereka.

### 13.3 Proses Bisnis — Verifikasi Bengkel Mitra

1. Calon bengkel mitra melakukan registrasi melalui halaman Registrasi Bengkel.
2. Data bengkel (dokumen legalitas, data pemilik, lokasi) masuk ke antrean verifikasi Super Admin.
3. Super Admin meninjau kelengkapan dan keabsahan data.
4. Super Admin menyetujui atau menolak pendaftaran bengkel.
5. Jika disetujui, bengkel berstatus "Terverifikasi" dan dapat mulai beroperasi dalam sistem (menambahkan pegawai, melakukan scan QR, dll).
6. Jika ditolak, bengkel menerima notifikasi alasan penolakan dan dapat mengajukan ulang dengan perbaikan data.

### 13.4 Proses Bisnis — Transfer Kepemilikan Kendaraan

1. Pemilik lama (pelanggan) membuka fitur Transfer Kepemilikan pada kendaraan yang akan dijual/dipindahtangankan.
2. Pemilik lama memasukkan data penerima (email/nomor HP calon pemilik baru yang sudah/akan terdaftar di Maintify).
3. Sistem mengirimkan permintaan verifikasi kepada calon penerima.
4. Calon penerima menyetujui permintaan transfer melalui akunnya.
5. Sistem menampilkan halaman review kepada pemilik lama berisi ringkasan data yang akan ditransfer beserta disclaimer.
6. Pemilik lama mengonfirmasi transfer final.
7. Sistem memindahkan kepemilikan data kendaraan (termasuk seluruh riwayat service) ke akun pemilik baru, dan mencabut akses pemilik lama terhadap kendaraan tersebut.
8. Kedua pihak menerima notifikasi bahwa transfer telah berhasil.

---

## 14. PRODUCT FLOW

### 14.1 Flow Umum Aplikasi (High Level)

- Landing/Login → Register (jika baru) → Dashboard → Kelola Kendaraan → Lihat QR Code → Bengkel Melakukan Service → Riwayat Terupdate → Notifikasi Pengingat → Siklus Berulang

### 14.2 Flow Detail — Pelanggan Baru

- Buka Website → Klik "Daftar" → Isi Form Register (Nama, Email, No HP, Password, Domisili) → Verifikasi Email/HP (jika diaktifkan) → Login → Diarahkan ke Dashboard (kosong) → Klik "Tambah Kendaraan" → Isi Data Kendaraan → Sistem Generate QR Code → Unduh/Cetak QR Code → Tempelkan pada Kendaraan

### 14.3 Flow Detail — Pegawai Bengkel Mencatat Service

- Login sebagai Pegawai Bengkel → Klik "Scan QR Kendaraan" → Arahkan Kamera ke QR Code → Sistem Menampilkan Data Kendaraan → Klik "Tambah Riwayat Service" → Isi Form (Jenis Service, Sparepart, Oli, Odometer, Catatan) → Simpan → Data Tersimpan dan Terlihat oleh Pelanggan

### 14.4 Flow Detail — Pencarian Bengkel Terdekat

- Login sebagai Pelanggan → Buka Menu "Bengkel Terdekat" → Sistem Meminta Izin Lokasi → Tampilkan Peta dan Daftar Bengkel Terverifikasi Terdekat → Filter (Jarak, Rating, Jenis Layanan) → Pilih Bengkel → Lihat Detail Bengkel → Klik "Petunjuk Arah" (Directions)

### 14.5 Flow Detail — Verifikasi Bengkel oleh Super Admin

- Login Super Admin (dengan OTP) → Dashboard Sistem → Menu "Verifikasi Bengkel" → Pilih Pengajuan Baru → Tinjau Dokumen dan Data → Klik "Setujui" / "Tolak" → Sistem Mengirim Notifikasi ke Bengkel → Status Bengkel Terupdate


---

## 15. FUNCTIONAL REQUIREMENTS

Functional Requirements dikelompokkan berdasarkan modul/fitur utama. Setiap requirement memiliki ID unik (FR-XXX) untuk memudahkan pelacakan (traceability) ke User Story, Test Case, dan Acceptance Criteria.

### 15.1 Modul Autentikasi (Login & Register)

| ID | Requirement | Prioritas |
|---|---|---|
| FR-001 | Sistem harus menyediakan form login dengan input email dan password | Must Have |
| FR-002 | Sistem harus memvalidasi kredensial login dan menampilkan pesan error jika tidak sesuai | Must Have |
| FR-003 | Sistem harus menyediakan opsi "Remember Me" untuk menyimpan sesi login | Should Have |
| FR-004 | Sistem harus menyediakan fitur "Forgot Password" dengan pengiriman link reset melalui email | Must Have |
| FR-005 | Sistem harus menyediakan form register dengan input Nama, Email, No HP, Password, Konfirmasi Password, dan Domisili | Must Have |
| FR-006 | Sistem harus memvalidasi format email dan nomor HP saat registrasi | Must Have |
| FR-007 | Sistem harus memvalidasi kesesuaian Password dan Konfirmasi Password | Must Have |
| FR-008 | Sistem harus mencegah pendaftaran ganda menggunakan email yang sama | Must Have |
| FR-009 | Sistem harus menyediakan tombol ganti bahasa pada halaman login (disiapkan untuk pengembangan multi-bahasa) | Could Have |
| FR-010 | Sistem harus menampilkan badge "Real-Time" dan "Digital ID" sebagai penguat value proposition pada halaman login | Could Have |
| FR-011 | Sistem harus menampilkan link menuju halaman Register dari halaman Login dan sebaliknya | Must Have |
| FR-012 | Sistem harus menampilkan card promosi bengkel mitra pada halaman login sebagai konten marketing | Could Have |

### 15.2 Modul Dashboard Beranda (Pelanggan)

| ID | Requirement | Prioritas |
|---|---|---|
| FR-013 | Sistem harus menampilkan sidebar navigasi utama pada dashboard | Must Have |
| FR-014 | Sistem harus menampilkan Hero Vehicle Card berisi ringkasan kendaraan utama pengguna | Must Have |
| FR-015 | Sistem harus menampilkan statistik kendaraan (jumlah kendaraan, status kesehatan rata-rata) | Must Have |
| FR-016 | Sistem harus menampilkan riwayat service terakhir pada dashboard | Must Have |
| FR-017 | Sistem harus menyediakan tombol akses cepat menuju Digital ID (QR Code) kendaraan | Must Have |
| FR-018 | Dashboard harus dapat menampilkan multiple kendaraan apabila pengguna memiliki lebih dari satu kendaraan | Must Have |

### 15.3 Modul My Vehicles (Daftar Kendaraan)

| ID | Requirement | Prioritas |
|---|---|---|
| FR-019 | Sistem harus menampilkan daftar seluruh kendaraan milik pengguna dalam bentuk card | Must Have |
| FR-020 | Sistem harus menyediakan fitur pencarian kendaraan berdasarkan nama/plat nomor | Should Have |
| FR-021 | Sistem harus menyediakan tombol "Add Vehicle" untuk menambahkan kendaraan baru | Must Have |
| FR-022 | Setiap vehicle card harus menampilkan badge status (Aktif, Perlu Service, Bermasalah) | Must Have |
| FR-023 | Setiap vehicle card harus menampilkan informasi mileage (jarak tempuh) | Must Have |
| FR-024 | Setiap vehicle card harus menampilkan informasi fuel (jenis/level bahan bakar jika tersedia) | Should Have |
| FR-025 | Setiap vehicle card harus menampilkan indikator health (kesehatan kendaraan) | Must Have |
| FR-026 | Setiap vehicle card harus menampilkan indikator oil life (sisa umur oli) | Must Have |

### 15.4 Modul Detail Kendaraan

| ID | Requirement | Prioritas |
|---|---|---|
| FR-027 | Sistem harus menampilkan status kendaraan secara keseluruhan pada halaman detail | Must Have |
| FR-028 | Sistem harus menampilkan statistik kendaraan (total service, total biaya jika tersedia, rata-rata interval service) | Should Have |
| FR-029 | Sistem harus menampilkan timeline service dalam urutan kronologis | Must Have |
| FR-030 | Sistem harus menampilkan daftar sparepart yang pernah diganti pada kendaraan tersebut | Must Have |
| FR-031 | Sistem harus menampilkan catatan mekanik dari setiap sesi service | Should Have |
| FR-032 | Sistem harus menyediakan akses ke QR Code kendaraan dari halaman detail | Must Have |
| FR-033 | Sistem harus menyediakan akses ke fitur Transfer Kepemilikan dari halaman detail kendaraan | Must Have |

### 15.5 Modul Riwayat Service

| ID | Requirement | Prioritas |
|---|---|---|
| FR-034 | Sistem harus menampilkan riwayat service dalam format timeline | Must Have |
| FR-035 | Sistem harus menampilkan detail setiap entri riwayat (tanggal, bengkel, jenis service, biaya jika ada) | Must Have |
| FR-036 | Sistem harus menampilkan statistik ringkas riwayat service (frekuensi, rata-rata interval) | Should Have |
| FR-037 | Sistem harus menampilkan badge status pada setiap entri riwayat (Selesai, Dalam Proses) | Should Have |
| FR-038 | Sistem harus mengizinkan pengguna memfilter riwayat berdasarkan rentang tanggal atau jenis service | Should Have |

### 15.6 Modul QR Code Kendaraan

| ID | Requirement | Prioritas |
|---|---|---|
| FR-039 | Sistem harus men-generate QR Code unik untuk setiap kendaraan yang terdaftar | Must Have |
| FR-040 | Sistem harus menampilkan QR Code beserta data pendukung (VIN, Plate Number, Engine Type) | Must Have |
| FR-041 | Sistem harus menampilkan status verifikasi kendaraan (Verified/Unverified) | Must Have |
| FR-042 | Sistem harus menyediakan tombol untuk mengunduh QR Code dalam format gambar (PNG/PDF) | Must Have |
| FR-043 | Sistem harus menampilkan banner keamanan yang menjelaskan pentingnya menjaga QR Code | Should Have |
| FR-044 | QR Code harus dapat dipindai oleh perangkat bengkel mitra untuk mengakses data kendaraan secara langsung | Must Have |
| FR-045 | Sistem harus mencatat log setiap kali QR Code dipindai (waktu, bengkel yang memindai) | Should Have |

### 15.7 Modul Bengkel Terdekat

| ID | Requirement | Prioritas |
|---|---|---|
| FR-046 | Sistem harus menyediakan fitur pencarian bengkel berdasarkan kata kunci/lokasi | Must Have |
| FR-047 | Sistem harus menyediakan filter pencarian (jarak, rating, jenis layanan, status verifikasi) | Should Have |
| FR-048 | Sistem harus menampilkan peta interaktif dengan titik lokasi bengkel mitra | Must Have |
| FR-049 | Sistem harus menampilkan workshop card berisi ringkasan info bengkel (nama, jarak, rating) | Must Have |
| FR-050 | Sistem harus menyediakan tombol "Directions" menuju aplikasi peta eksternal atau peta internal | Must Have |
| FR-051 | Sistem harus menampilkan badge "Verified Partner" untuk bengkel yang telah terverifikasi Super Admin | Must Have |

### 15.8 Modul Tambah Kendaraan

| ID | Requirement | Prioritas |
|---|---|---|
| FR-052 | Sistem harus menyediakan fitur upload foto kendaraan | Should Have |
| FR-053 | Sistem harus menyediakan form data kendaraan (merek, model, tahun, plat nomor, VIN, warna) | Must Have |
| FR-054 | Sistem harus menyediakan pilihan fuel type (bensin, diesel, listrik, hybrid) | Must Have |
| FR-055 | Sistem harus menyediakan input odometer awal saat pendaftaran kendaraan | Must Have |
| FR-056 | Sistem harus memvalidasi keunikan VIN dan plat nomor agar tidak terjadi duplikasi data kendaraan | Must Have |
| FR-057 | Sistem harus men-generate QR Code otomatis setelah data kendaraan berhasil disimpan | Must Have |

### 15.9 Modul Settings

| ID | Requirement | Prioritas |
|---|---|---|
| FR-058 | Sistem harus menyediakan halaman pengelolaan profil (nama, foto, kontak) | Must Have |
| FR-059 | Sistem harus menyediakan pengaturan akun (ubah password, ubah email) | Must Have |
| FR-060 | Sistem harus menyediakan akses cepat ke daftar kendaraan dari halaman settings | Should Have |
| FR-061 | Sistem harus menyediakan pengaturan notifikasi (aktif/nonaktif pengingat service) | Must Have |
| FR-062 | Sistem harus menyediakan tombol logout | Must Have |

### 15.10 Modul Transfer Kepemilikan

| ID | Requirement | Prioritas |
|---|---|---|
| FR-063 | Sistem harus menampilkan ringkasan data kendaraan yang akan ditransfer | Must Have |
| FR-064 | Sistem harus menyediakan form input data penerima (email/nomor HP) | Must Have |
| FR-065 | Sistem harus melakukan verifikasi keberadaan akun penerima dalam sistem | Must Have |
| FR-066 | Sistem harus menampilkan halaman review sebelum transfer difinalisasi | Must Have |
| FR-067 | Sistem harus menampilkan disclaimer bahwa transfer bersifat permanen dan tidak dapat dibatalkan sepihak | Must Have |
| FR-068 | Sistem harus meminta konfirmasi dari kedua belah pihak (pemilik lama dan penerima) sebelum transfer difinalisasi | Must Have |
| FR-069 | Sistem harus mencabut akses pemilik lama terhadap kendaraan setelah transfer berhasil | Must Have |
| FR-070 | Sistem harus mempertahankan seluruh riwayat service kendaraan setelah proses transfer | Must Have |

### 15.11 Modul Transfer Berhasil

| ID | Requirement | Prioritas |
|---|---|---|
| FR-071 | Sistem harus menampilkan halaman success state setelah transfer berhasil | Must Have |
| FR-072 | Sistem harus menampilkan detail transfer (kendaraan, tanggal, pemilik baru) | Must Have |
| FR-073 | Sistem harus menyediakan tombol kembali ke halaman utama (Home) | Must Have |
| FR-074 | Sistem harus mengirimkan notifikasi email/in-app kepada kedua pihak setelah transfer selesai | Should Have |

### 15.12 Modul Login Admin (Admin Bengkel)

| ID | Requirement | Prioritas |
|---|---|---|
| FR-075 | Sistem harus menyediakan halaman login terpisah untuk Admin Bengkel | Must Have |
| FR-076 | Sistem harus menyediakan opsi "Remember Me" pada login admin | Should Have |
| FR-077 | Sistem harus menyediakan fitur "Forgot Password" pada login admin | Must Have |
| FR-078 | Sistem harus menolak login apabila status bengkel belum terverifikasi oleh Super Admin | Must Have |

### 15.13 Modul Registrasi Bengkel (Registrasi Mitra)

| ID | Requirement | Prioritas |
|---|---|---|
| FR-079 | Sistem harus menyediakan form data bengkel (nama bengkel, alamat, jenis layanan, dokumen legalitas) | Must Have |
| FR-080 | Sistem harus menyediakan form data pemilik bengkel (nama, email, no HP, KTP) | Must Have |
| FR-081 | Sistem harus menyediakan input password untuk akun Admin Bengkel | Must Have |
| FR-082 | Sistem harus mengirimkan data pendaftaran ke antrean verifikasi Super Admin setelah submit | Must Have |
| FR-083 | Sistem harus menampilkan status pengajuan (Menunggu Verifikasi, Disetujui, Ditolak) kepada calon mitra | Must Have |

### 15.14 Modul Login Super Admin

| ID | Requirement | Prioritas |
|---|---|---|
| FR-084 | Sistem harus menyediakan halaman login khusus Super Admin dengan input Username dan Password | Must Have |
| FR-085 | Sistem harus menerapkan verifikasi dua faktor (OTP) untuk login Super Admin | Must Have |
| FR-086 | Sistem harus menolak akses apabila OTP tidak valid atau kedaluwarsa | Must Have |
| FR-087 | Sistem harus mencatat setiap upaya login Super Admin ke dalam audit log | Must Have |

### 15.15 Modul Scan QR (Pegawai Bengkel)

| ID | Requirement | Prioritas |
|---|---|---|
| FR-088 | Sistem harus menyediakan fitur pemindaian QR Code menggunakan kamera perangkat | Must Have |
| FR-089 | Sistem harus menampilkan data kendaraan segera setelah QR Code berhasil dipindai | Must Have |
| FR-090 | Sistem harus menampilkan pesan error apabila QR Code tidak valid atau tidak terdaftar | Must Have |
| FR-091 | Sistem harus menyediakan form tambah riwayat service setelah QR Code berhasil diidentifikasi | Must Have |
| FR-092 | Sistem harus mengizinkan pegawai bengkel mengubah riwayat service yang baru saja diinput (dalam batas waktu tertentu) | Should Have |
| FR-093 | Sistem harus menyediakan modul pengelolaan sparepart yang digunakan dalam service | Must Have |
| FR-094 | Sistem harus menampilkan daftar pelanggan yang pernah dilayani oleh bengkel tersebut | Must Have |

### 15.16 Modul Dashboard Admin Bengkel

| ID | Requirement | Prioritas |
|---|---|---|
| FR-095 | Sistem harus menampilkan dashboard ringkasan operasional bengkel (jumlah service harian/bulanan, pegawai aktif) | Must Have |
| FR-096 | Sistem harus menyediakan modul pengelolaan pegawai (tambah, edit, nonaktifkan akun pegawai) | Must Have |
| FR-097 | Sistem harus menyediakan modul pengelolaan histori service untuk keperluan audit internal bengkel | Should Have |
| FR-098 | Sistem harus menyediakan modul pengelolaan data pelanggan bengkel | Must Have |
| FR-099 | Sistem harus menyediakan modul pengelolaan data profil bengkel (jam operasional, alamat, kontak) | Must Have |
| FR-100 | Sistem harus menyediakan modul laporan operasional (jumlah service, sparepart terlaris, dsb) yang dapat diunduh | Should Have |

### 15.17 Modul Dashboard Super Admin

| ID | Requirement | Prioritas |
|---|---|---|
| FR-101 | Sistem harus menampilkan dashboard sistem dengan ringkasan statistik platform (total pengguna, total bengkel, total kendaraan) | Must Have |
| FR-102 | Sistem harus menyediakan modul verifikasi bengkel mitra baru | Must Have |
| FR-103 | Sistem harus menyediakan modul pengelolaan seluruh pengguna platform | Must Have |
| FR-104 | Sistem harus menyediakan modul pengelolaan seluruh data kendaraan | Must Have |
| FR-105 | Sistem harus menyediakan modul pengelolaan seluruh bengkel mitra | Must Have |
| FR-106 | Sistem harus menyediakan modul monitoring kesehatan sistem (uptime, error rate) | Should Have |
| FR-107 | Sistem harus menyediakan pengaturan global platform (kebijakan, parameter sistem) | Should Have |
| FR-108 | Sistem harus menyediakan modul audit log yang mencatat seluruh aktivitas penting dalam sistem | Must Have |

### 15.18 Modul Notifikasi & Pengingat Service

| ID | Requirement | Prioritas |
|---|---|---|
| FR-109 | Sistem harus mengirimkan notifikasi pengingat service berdasarkan interval waktu (misal setiap 3 bulan) | Must Have |
| FR-110 | Sistem harus mengirimkan notifikasi pengingat service berdasarkan jarak tempuh (misal setiap 3.000 km) | Must Have |
| FR-111 | Sistem harus mengirimkan notifikasi saat riwayat service baru ditambahkan oleh bengkel | Must Have |
| FR-112 | Sistem harus mengirimkan notifikasi status pengajuan verifikasi bengkel | Must Have |
| FR-113 | Sistem harus mengirimkan notifikasi terkait proses transfer kepemilikan kendaraan | Must Have |


---

## 16. NON FUNCTIONAL REQUIREMENTS

| ID | Kategori | Requirement |
|---|---|---|
| NFR-001 | Performance | Waktu muat halaman (page load time) tidak boleh lebih dari 3 detik pada koneksi 4G standar |
| NFR-002 | Performance | Proses scan QR Code harus menampilkan hasil dalam waktu maksimal 2 detik |
| NFR-003 | Scalability | Sistem harus mampu menangani minimal 10.000 pengguna aktif bersamaan tanpa penurunan performa signifikan |
| NFR-004 | Availability | Sistem harus memiliki uptime minimal 99.5% per bulan |
| NFR-005 | Security | Seluruh data sensitif (password, data pribadi) harus dienkripsi baik saat disimpan (at rest) maupun saat transmisi (in transit) menggunakan TLS 1.2 ke atas |
| NFR-006 | Security | Sistem harus menerapkan hashing password menggunakan algoritma yang aman (bcrypt/argon2) |
| NFR-007 | Security | Login Super Admin wajib menggunakan autentikasi dua faktor (OTP) |
| NFR-008 | Usability | Antarmuka harus responsif dan dapat digunakan dengan baik pada perangkat desktop, tablet, dan mobile |
| NFR-009 | Usability | Sistem harus mengikuti prinsip aksesibilitas dasar (kontras warna memadai, ukuran font terbaca) |
| NFR-010 | Reliability | Data riwayat service tidak boleh hilang meskipun terjadi kegagalan sistem sementara (harus ada mekanisme backup otomatis) |
| NFR-011 | Maintainability | Kode sistem harus mengikuti standar coding convention yang konsisten dan terdokumentasi |
| NFR-012 | Portability | Website harus kompatibel dengan browser modern (Chrome, Firefox, Safari, Edge) versi 2 tahun terakhir |
| NFR-013 | Data Integrity | Sistem harus memastikan data riwayat service tidak dapat dihapus secara permanen oleh Pegawai Bengkel maupun Admin Bengkel (soft delete/audit trail) |
| NFR-014 | Compliance | Sistem harus mematuhi regulasi perlindungan data pribadi yang berlaku di Indonesia (UU PDP) |
| NFR-015 | Backup & Recovery | Sistem harus melakukan backup data harian dan memiliki mekanisme disaster recovery dengan RPO maksimal 24 jam |
| NFR-016 | Localization | Sistem menggunakan Bahasa Indonesia sebagai bahasa utama dengan struktur yang mendukung penambahan bahasa lain di masa depan |
| NFR-017 | Auditability | Seluruh aktivitas krusial (perubahan data kendaraan, transfer kepemilikan, verifikasi bengkel) harus tercatat dalam audit log dengan timestamp dan identitas pelaku |
| NFR-018 | Interoperability | API sistem harus mengikuti standar RESTful dan format data JSON agar mudah diintegrasikan di masa depan |

---

## 17. USER STORIES

| ID | Sebagai | Saya ingin | Sehingga | Terkait FR |
|---|---|---|---|---|
| US-001 | Pelanggan | Mendaftar akun baru | Saya dapat mulai menggunakan layanan Maintify | FR-005 |
| US-002 | Pelanggan | Login ke akun saya | Saya dapat mengakses dashboard kendaraan saya | FR-001, FR-002 |
| US-003 | Pelanggan | Menambahkan kendaraan baru | Kendaraan saya terdaftar dan mendapatkan QR Code | FR-052–FR-057 |
| US-004 | Pelanggan | Melihat QR Code kendaraan saya | Saya dapat mengunduh dan menempelkannya pada kendaraan | FR-039–FR-042 |
| US-005 | Pelanggan | Melihat riwayat service kendaraan saya | Saya mengetahui histori perawatan kendaraan secara lengkap | FR-034–FR-038 |
| US-006 | Pelanggan | Melihat dashboard ringkasan kondisi kendaraan | Saya dapat memantau kesehatan kendaraan secara cepat | FR-013–FR-018 |
| US-007 | Pelanggan | Mencari bengkel terdekat | Saya dapat menemukan bengkel terpercaya di sekitar lokasi saya | FR-046–FR-051 |
| US-008 | Pelanggan | Mentransfer kepemilikan kendaraan | Kendaraan yang saya jual tetap memiliki riwayat service yang utuh untuk pemilik baru | FR-063–FR-070 |
| US-009 | Pelanggan | Menerima pengingat service | Saya tidak lupa jadwal perawatan kendaraan saya | FR-109–FR-110 |
| US-010 | Pelanggan | Mengelola pengaturan akun saya | Saya dapat memperbarui data profil dan preferensi notifikasi | FR-058–FR-062 |
| US-011 | Pegawai Bengkel | Memindai QR Code kendaraan pelanggan | Saya dapat langsung mengakses data kendaraan tanpa input manual | FR-088–FR-090 |
| US-012 | Pegawai Bengkel | Menambahkan riwayat service baru | Data service tercatat otomatis ke akun pelanggan | FR-091 |
| US-013 | Pegawai Bengkel | Mengelola data sparepart yang digunakan | Laporan penggunaan sparepart lebih akurat | FR-093 |
| US-014 | Admin Bengkel | Melihat dashboard operasional bengkel | Saya dapat memantau kinerja bengkel secara keseluruhan | FR-095 |
| US-015 | Admin Bengkel | Mengelola data pegawai | Saya dapat menambah/menonaktifkan akun pegawai sesuai kebutuhan operasional | FR-096 |
| US-016 | Admin Bengkel | Melihat laporan operasional bengkel | Saya dapat mengambil keputusan bisnis berdasarkan data nyata | FR-100 |
| US-017 | Super Admin | Memverifikasi pendaftaran bengkel baru | Hanya bengkel yang valid dapat bergabung dalam ekosistem Maintify | FR-102 |
| US-018 | Super Admin | Memantau statistik platform secara keseluruhan | Saya dapat mengawasi pertumbuhan dan kesehatan platform | FR-101 |
| US-019 | Super Admin | Mengakses audit log sistem | Saya dapat melacak aktivitas mencurigakan atau kesalahan sistem | FR-108 |
| US-020 | Calon Mitra Bengkel | Mendaftarkan bengkel saya ke Maintify | Bengkel saya dapat menjadi mitra resmi dan menerima pelanggan baru | FR-079–FR-083 |

---

## 18. ACCEPTANCE CRITERIA

Acceptance Criteria disusun menggunakan format Given-When-Then untuk fitur-fitur utama.

### AC untuk US-001 (Register Pelanggan)

- **Given** pengguna berada di halaman Register,
  **When** pengguna mengisi seluruh field wajib dengan data valid dan menekan tombol Daftar,
  **Then** sistem membuat akun baru dan mengarahkan pengguna ke halaman Login/Dashboard.
- **Given** pengguna mengisi email yang sudah terdaftar,
  **When** pengguna menekan tombol Daftar,
  **Then** sistem menampilkan pesan error "Email sudah terdaftar".
- **Given** pengguna mengisi Password dan Konfirmasi Password yang tidak sama,
  **When** pengguna menekan tombol Daftar,
  **Then** sistem menampilkan pesan error validasi dan mencegah submit.

### AC untuk US-002 (Login)

- **Given** pengguna memasukkan email dan password yang benar,
  **When** pengguna menekan tombol Login,
  **Then** sistem mengarahkan pengguna ke Dashboard sesuai role-nya.
- **Given** pengguna memasukkan password yang salah,
  **When** pengguna menekan tombol Login,
  **Then** sistem menampilkan pesan error "Email atau password salah" tanpa menyebutkan field mana yang salah (demi keamanan).

### AC untuk US-003 (Tambah Kendaraan)

- **Given** pengguna berada di halaman Tambah Kendaraan,
  **When** pengguna mengisi seluruh data wajib dan menekan Simpan,
  **Then** sistem menyimpan data kendaraan dan men-generate QR Code baru secara otomatis.
- **Given** pengguna memasukkan VIN yang sudah terdaftar pada kendaraan lain,
  **When** pengguna menekan Simpan,
  **Then** sistem menampilkan pesan error "VIN sudah terdaftar" dan mencegah penyimpanan data duplikat.

### AC untuk US-011 (Scan QR Code oleh Pegawai Bengkel)

- **Given** pegawai bengkel mengarahkan kamera ke QR Code kendaraan yang valid,
  **When** proses scan berhasil,
  **Then** sistem menampilkan data kendaraan dan riwayat service dalam waktu maksimal 2 detik.
- **Given** pegawai bengkel memindai QR Code yang tidak terdaftar dalam sistem,
  **When** proses scan selesai,
  **Then** sistem menampilkan pesan error "QR Code tidak valid atau tidak ditemukan".

### AC untuk US-008 (Transfer Kepemilikan)

- **Given** pemilik lama telah mengisi data penerima dan penerima telah menyetujui permintaan transfer,
  **When** pemilik lama mengonfirmasi transfer pada halaman review,
  **Then** sistem memindahkan seluruh data dan riwayat kendaraan ke akun penerima, mencabut akses pemilik lama, dan menampilkan halaman Transfer Berhasil.
- **Given** penerima belum memiliki akun terdaftar di Maintify,
  **When** pemilik lama memasukkan data penerima,
  **Then** sistem menampilkan opsi untuk mengundang penerima mendaftar terlebih dahulu sebelum transfer dapat dilanjutkan.

### AC untuk US-017 (Verifikasi Bengkel oleh Super Admin)

- **Given** Super Admin meninjau data pengajuan bengkel yang lengkap dan valid,
  **When** Super Admin menekan tombol Setujui,
  **Then** status bengkel berubah menjadi "Terverifikasi" dan bengkel menerima notifikasi persetujuan serta dapat login ke sistem.
- **Given** data pengajuan bengkel tidak lengkap atau tidak valid,
  **When** Super Admin menekan tombol Tolak,
  **Then** sistem meminta alasan penolakan dan mengirimkan notifikasi kepada calon mitra beserta alasannya.


---

## 19. USE CASE DESCRIPTION

### UC-01: Pendaftaran Kendaraan Baru

| Atribut | Deskripsi |
|---|---|
| Aktor | Pelanggan |
| Deskripsi | Pelanggan mendaftarkan kendaraan baru ke dalam sistem untuk mendapatkan QR Code |
| Precondition | Pelanggan telah login ke sistem |
| Main Flow | 1. Pelanggan membuka halaman Tambah Kendaraan. 2. Pelanggan mengisi data kendaraan. 3. Sistem memvalidasi data. 4. Sistem menyimpan data dan men-generate QR Code. 5. Sistem menampilkan halaman QR Code kendaraan. |
| Alternative Flow | Jika VIN/Plat Nomor sudah terdaftar, sistem menampilkan pesan error dan meminta pengguna memperbaiki data. |
| Postcondition | Kendaraan baru tersimpan dalam sistem dan memiliki QR Code aktif |

### UC-02: Pemindaian QR Code dan Pencatatan Service

| Atribut | Deskripsi |
|---|---|
| Aktor | Pegawai Bengkel |
| Deskripsi | Pegawai bengkel memindai QR Code kendaraan dan mencatat hasil service |
| Precondition | Pegawai bengkel telah login, bengkel berstatus terverifikasi |
| Main Flow | 1. Pegawai membuka fitur Scan QR. 2. Kamera memindai QR Code kendaraan. 3. Sistem menampilkan data kendaraan. 4. Pegawai mengisi form riwayat service baru. 5. Sistem menyimpan data riwayat service. |
| Alternative Flow | Jika QR Code tidak valid, sistem menampilkan pesan error dan meminta pemindaian ulang. |
| Postcondition | Riwayat service baru tersimpan dan dapat dilihat oleh pelanggan pemilik kendaraan |

### UC-03: Transfer Kepemilikan Kendaraan

| Atribut | Deskripsi |
|---|---|
| Aktor | Pelanggan (Pemilik Lama & Penerima) |
| Deskripsi | Pemilik lama memindahkan kepemilikan data kendaraan ke pemilik baru |
| Precondition | Kendaraan terdaftar atas nama pemilik lama, penerima memiliki akun Maintify |
| Main Flow | 1. Pemilik lama membuka menu Transfer Kepemilikan. 2. Mengisi data penerima. 3. Sistem mengirim permintaan ke penerima. 4. Penerima menyetujui. 5. Pemilik lama meninjau dan mengonfirmasi transfer. 6. Sistem memindahkan data kendaraan. |
| Alternative Flow | Jika penerima menolak permintaan, proses transfer dibatalkan dan pemilik lama menerima notifikasi. |
| Postcondition | Kepemilikan kendaraan berpindah ke akun penerima beserta seluruh riwayat service |

### UC-04: Verifikasi Bengkel Mitra

| Atribut | Deskripsi |
|---|---|
| Aktor | Super Admin |
| Deskripsi | Super Admin meninjau dan memvalidasi pendaftaran bengkel baru |
| Precondition | Bengkel telah mengajukan pendaftaran melalui halaman Registrasi Bengkel |
| Main Flow | 1. Super Admin membuka menu Verifikasi Bengkel. 2. Meninjau data dan dokumen bengkel. 3. Menyetujui atau menolak pengajuan. |
| Alternative Flow | Jika data tidak lengkap, Super Admin dapat meminta bengkel melengkapi data sebelum diproses lebih lanjut. |
| Postcondition | Status bengkel berubah menjadi Terverifikasi atau Ditolak |

### UC-05: Pencarian Bengkel Terdekat

| Atribut | Deskripsi |
|---|---|
| Aktor | Pelanggan |
| Deskripsi | Pelanggan mencari bengkel mitra terverifikasi di sekitar lokasinya |
| Precondition | Pelanggan mengizinkan akses lokasi perangkat |
| Main Flow | 1. Pelanggan membuka menu Bengkel Terdekat. 2. Sistem menampilkan peta dan daftar bengkel terdekat. 3. Pelanggan memfilter/mencari bengkel sesuai kebutuhan. 4. Pelanggan memilih bengkel dan melihat detail. |
| Alternative Flow | Jika lokasi tidak tersedia, sistem meminta pelanggan memasukkan lokasi secara manual. |
| Postcondition | Pelanggan mendapatkan daftar bengkel relevan beserta arah menuju lokasi |

---

## 20. ACTIVITY FLOW

### Activity Flow — Proses Service Kendaraan End-to-End

1. Pelanggan datang ke bengkel mitra.
2. Pegawai bengkel login ke sistem.
3. Pegawai bengkel membuka fitur Scan QR.
4. Sistem meminta akses kamera perangkat.
5. Pegawai mengarahkan kamera ke QR Code kendaraan.
6. Sistem melakukan validasi QR Code:
   - Jika valid → lanjut ke langkah 7.
   - Jika tidak valid → tampilkan error, kembali ke langkah 5.
7. Sistem menampilkan data kendaraan dan riwayat service.
8. Pegawai melakukan pekerjaan service secara fisik pada kendaraan.
9. Pegawai membuka form Tambah Riwayat Service.
10. Pegawai mengisi data service (jenis service, sparepart, oli, odometer, catatan mekanik).
11. Sistem memvalidasi kelengkapan form:
    - Jika lengkap → lanjut ke langkah 12.
    - Jika tidak lengkap → tampilkan error validasi, kembali ke langkah 10.
12. Sistem menyimpan data riwayat service baru.
13. Sistem memperbarui status kesehatan kendaraan dan sisa umur oli secara otomatis.
14. Sistem mengirimkan notifikasi kepada pelanggan bahwa riwayat service baru telah ditambahkan.
15. Proses selesai.

### Activity Flow — Transfer Kepemilikan Kendaraan

1. Pemilik lama membuka halaman Detail Kendaraan.
2. Pemilik lama memilih menu Transfer Kepemilikan.
3. Sistem menampilkan ringkasan data kendaraan.
4. Pemilik lama mengisi data penerima.
5. Sistem memeriksa apakah penerima memiliki akun terdaftar:
   - Jika ada → lanjut ke langkah 6.
   - Jika tidak ada → sistem menawarkan opsi mengundang penerima mendaftar, proses ditunda hingga penerima mendaftar.
6. Sistem mengirim permintaan persetujuan transfer ke akun penerima.
7. Penerima meninjau permintaan:
   - Jika disetujui → lanjut ke langkah 8.
   - Jika ditolak → proses dibatalkan, pemilik lama menerima notifikasi penolakan.
8. Sistem menampilkan halaman review kepada pemilik lama.
9. Pemilik lama membaca disclaimer dan mengonfirmasi transfer final.
10. Sistem memindahkan seluruh data kendaraan (termasuk riwayat service) ke akun penerima.
11. Sistem mencabut akses pemilik lama terhadap kendaraan tersebut.
12. Sistem menampilkan halaman Transfer Berhasil kepada pemilik lama.
13. Sistem mengirimkan notifikasi konfirmasi kepada kedua belah pihak.
14. Proses selesai.


---

## 21. NAVIGATION STRUCTURE

### 21.1 Navigasi Pelanggan

- Dashboard Beranda
  - My Vehicles
    - Detail Kendaraan
      - Riwayat Service
      - QR Code Kendaraan
      - Transfer Kepemilikan
        - Transfer Berhasil
    - Tambah Kendaraan
  - Bengkel Terdekat
  - Settings
    - Profil
    - Pengaturan Akun
    - Notifikasi
    - Logout

### 21.2 Navigasi Pegawai Bengkel

- Dashboard Pegawai
  - Scan QR Kendaraan
    - Detail Kendaraan (view)
    - Tambah Riwayat Service
  - Kelola Sparepart
  - Daftar Pelanggan

### 21.3 Navigasi Admin Bengkel

- Dashboard Admin Bengkel
  - Kelola Pegawai
  - Kelola Histori Service
  - Kelola Pelanggan
  - Kelola Data Bengkel
  - Kelola Kendaraan Pelanggan
  - Laporan

### 21.4 Navigasi Super Admin

- Dashboard Sistem
  - Verifikasi Bengkel
  - Kelola Pengguna
  - Kelola Kendaraan
  - Kelola Bengkel
  - Monitoring Sistem
  - Pengaturan Global
  - Audit Log

---

## 22. SITEMAP

```
Maintify (SERVTRACK)
│
├── Public
│   ├── Login (Pelanggan)
│   ├── Register (Pelanggan)
│   ├── Login Admin (Admin Bengkel)
│   ├── Registrasi Bengkel (Mitra)
│   └── Login Super Admin
│
├── Area Pelanggan (Authenticated)
│   ├── Dashboard Beranda
│   ├── My Vehicles
│   │   ├── Detail Kendaraan
│   │   │   ├── Riwayat Service
│   │   │   ├── QR Code Kendaraan
│   │   │   └── Transfer Kepemilikan → Transfer Berhasil
│   │   └── Tambah Kendaraan
│   ├── Bengkel Terdekat
│   └── Settings
│
├── Area Pegawai Bengkel (Authenticated)
│   ├── Dashboard Pegawai
│   ├── Scan QR Kendaraan
│   ├── Kelola Sparepart
│   └── Daftar Pelanggan
│
├── Area Admin Bengkel (Authenticated)
│   ├── Dashboard Admin Bengkel
│   ├── Kelola Pegawai
│   ├── Kelola Histori Service
│   ├── Kelola Pelanggan
│   ├── Kelola Data Bengkel
│   ├── Kelola Kendaraan Pelanggan
│   └── Laporan
│
└── Area Super Admin (Authenticated)
    ├── Dashboard Sistem
    ├── Verifikasi Bengkel
    ├── Kelola Pengguna
    ├── Kelola Kendaraan
    ├── Kelola Bengkel
    ├── Monitoring Sistem
    ├── Pengaturan Global
    └── Audit Log
```

---

## 23. INFORMATION ARCHITECTURE

| Level | Kategori | Konten |
|---|---|---|
| 1 | Autentikasi | Login, Register, Forgot Password, OTP |
| 2 | Beranda/Dashboard | Ringkasan kendaraan, statistik, akses cepat |
| 3 | Manajemen Kendaraan | Daftar kendaraan, tambah kendaraan, detail kendaraan |
| 4 | Riwayat & Data Service | Timeline service, sparepart, catatan mekanik |
| 5 | Identitas Digital | QR Code, VIN, status verifikasi |
| 6 | Pencarian & Lokasi | Bengkel terdekat, peta, direction |
| 7 | Kepemilikan | Transfer kepemilikan, review, konfirmasi |
| 8 | Pengaturan | Profil, akun, notifikasi, logout |
| 9 | Operasional Bengkel | Scan QR, kelola sparepart, daftar pelanggan, laporan |
| 10 | Administrasi Platform | Verifikasi bengkel, kelola pengguna, audit log, monitoring sistem |

Prinsip arsitektur informasi Maintify disusun secara **role-based hierarchical navigation**, di mana setiap role hanya dapat mengakses cabang informasi yang relevan dengan tanggung jawabnya, sementara data inti (kendaraan, riwayat service) tetap menjadi satu entitas terpusat yang direferensikan oleh seluruh role sesuai hak aksesnya masing-masing.


---

## 24. DATABASE OVERVIEW

Sistem Maintify menggunakan pendekatan **relational database** (contoh: PostgreSQL) sebagai basis data utama, mengingat kuatnya kebutuhan relasi antar entitas (pengguna, kendaraan, bengkel, riwayat service) serta pentingnya konsistensi transaksi (ACID), khususnya pada proses transfer kepemilikan kendaraan.

### 24.1 Daftar Entitas Utama

| Entitas | Deskripsi Singkat |
|---|---|
| users | Menyimpan data seluruh pengguna (Pelanggan, Pegawai Bengkel, Admin Bengkel, Super Admin) |
| vehicles | Menyimpan data kendaraan milik pelanggan |
| qr_codes | Menyimpan data QR Code yang terhubung dengan kendaraan |
| workshops | Menyimpan data bengkel mitra |
| workshop_staff | Menghubungkan pegawai dengan bengkel tempatnya bekerja |
| service_records | Menyimpan riwayat service kendaraan |
| service_parts | Menyimpan detail sparepart yang digunakan dalam suatu service |
| ownership_transfers | Menyimpan data proses transfer kepemilikan kendaraan |
| notifications | Menyimpan data notifikasi yang dikirim ke pengguna |
| audit_logs | Menyimpan log aktivitas penting dalam sistem |
| workshop_verification | Menyimpan proses dan status verifikasi bengkel oleh Super Admin |

Detail struktur tabel, tipe data, dan relasi antar entitas dijabarkan secara lengkap pada dokumen terpisah **`erd.md`**.

---

## 25. ENTITY RELATIONSHIP OVERVIEW

Ringkasan relasi antar entitas utama:

- Satu **User** (role Pelanggan) dapat memiliki banyak **Vehicle** (1 : N).
- Satu **Vehicle** memiliki tepat satu **QR Code** aktif (1 : 1).
- Satu **Vehicle** dapat memiliki banyak **Service Record** (1 : N).
- Satu **Service Record** dapat memiliki banyak **Service Part** (1 : N).
- Satu **Workshop** memiliki banyak **Workshop Staff** (1 : N), dan setiap Workshop Staff terhubung ke satu **User** (role Pegawai Bengkel).
- Satu **Workshop** dapat mencatat banyak **Service Record** (1 : N).
- Satu **Vehicle** dapat memiliki banyak riwayat **Ownership Transfer** (1 : N), yang menghubungkan User pengirim dan User penerima.
- Satu **Workshop** memiliki satu proses **Workshop Verification** (1 : 1) yang dikelola oleh Super Admin.
- Setiap aktivitas penting dari seluruh entitas di atas dapat menghasilkan satu entri pada **Audit Log** (N : 1 terhadap User pelaku).

Diagram visual ERD (format Mermaid/dbdiagram) tersedia lengkap pada file **`erd.md`**.

---

## 26. API REQUIREMENT

Seluruh komunikasi antara Frontend dan Backend menggunakan arsitektur **RESTful API** dengan format pertukaran data **JSON**, serta autentikasi menggunakan **JWT (JSON Web Token)**.

### 26.1 Autentikasi

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | /api/v1/auth/register | Registrasi akun pelanggan baru |
| POST | /api/v1/auth/login | Login pengguna (semua role) |
| POST | /api/v1/auth/logout | Logout pengguna |
| POST | /api/v1/auth/forgot-password | Permintaan reset password |
| POST | /api/v1/auth/reset-password | Reset password menggunakan token |
| POST | /api/v1/auth/otp/verify | Verifikasi OTP (khusus Super Admin) |
| POST | /api/v1/auth/refresh-token | Memperbarui access token |

### 26.2 Kendaraan

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | /api/v1/vehicles | Mengambil daftar kendaraan milik pengguna |
| POST | /api/v1/vehicles | Menambahkan kendaraan baru |
| GET | /api/v1/vehicles/{id} | Mengambil detail kendaraan |
| PUT | /api/v1/vehicles/{id} | Memperbarui data kendaraan |
| DELETE | /api/v1/vehicles/{id} | Menghapus (soft delete) data kendaraan |
| GET | /api/v1/vehicles/{id}/qr-code | Mengambil QR Code kendaraan |
| GET | /api/v1/vehicles/{id}/service-history | Mengambil riwayat service kendaraan |

### 26.3 QR Code & Scan

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | /api/v1/qr/{code}/resolve | Mengambil data kendaraan berdasarkan QR Code yang dipindai |
| POST | /api/v1/qr/{code}/scan-log | Mencatat log pemindaian QR Code |

### 26.4 Service Record

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | /api/v1/service-records | Menambahkan riwayat service baru |
| PUT | /api/v1/service-records/{id} | Memperbarui riwayat service |
| GET | /api/v1/service-records/{id} | Mengambil detail riwayat service |

### 26.5 Bengkel (Workshop)

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | /api/v1/workshops/nearby | Mengambil daftar bengkel terdekat berdasarkan koordinat |
| GET | /api/v1/workshops/{id} | Mengambil detail bengkel |
| POST | /api/v1/workshops/register | Registrasi bengkel mitra baru |
| GET | /api/v1/workshops/{id}/staff | Mengambil daftar pegawai bengkel |
| POST | /api/v1/workshops/{id}/staff | Menambahkan pegawai baru |

### 26.6 Transfer Kepemilikan

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | /api/v1/ownership-transfers | Memulai proses transfer kepemilikan |
| POST | /api/v1/ownership-transfers/{id}/approve | Persetujuan transfer oleh penerima |
| POST | /api/v1/ownership-transfers/{id}/confirm | Konfirmasi final oleh pemilik lama |
| GET | /api/v1/ownership-transfers/{id} | Mengambil detail status transfer |

### 26.7 Admin & Super Admin

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | /api/v1/admin/workshops/pending | Mengambil daftar bengkel menunggu verifikasi |
| POST | /api/v1/admin/workshops/{id}/verify | Menyetujui/menolak bengkel |
| GET | /api/v1/admin/users | Mengambil daftar seluruh pengguna |
| GET | /api/v1/admin/audit-logs | Mengambil data audit log |
| GET | /api/v1/admin/dashboard/stats | Mengambil statistik ringkasan platform |

### 26.8 Notifikasi

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | /api/v1/notifications | Mengambil daftar notifikasi pengguna |
| PUT | /api/v1/notifications/{id}/read | Menandai notifikasi sebagai telah dibaca |

---

## 27. SECURITY REQUIREMENT

| ID | Requirement |
|---|---|
| SEC-001 | Seluruh endpoint API (kecuali autentikasi publik) wajib menggunakan autentikasi token (JWT) |
| SEC-002 | Sistem harus menerapkan role-based access control (RBAC) pada setiap endpoint sesuai role pengguna |
| SEC-003 | Password wajib disimpan dalam bentuk hash (bcrypt/argon2), tidak boleh disimpan dalam plaintext |
| SEC-004 | Login Super Admin wajib menggunakan OTP sebagai lapisan keamanan tambahan |
| SEC-005 | Sistem harus menerapkan rate limiting pada endpoint login untuk mencegah brute-force attack |
| SEC-006 | QR Code kendaraan harus menggunakan token unik terenkripsi, bukan hanya ID sekuensial, untuk mencegah penebakan/spoofing |
| SEC-007 | Sistem harus memvalidasi origin request untuk mencegah CSRF pada aksi sensitif (transfer kepemilikan, perubahan data akun) |
| SEC-008 | Seluruh komunikasi data wajib melalui HTTPS/TLS |
| SEC-009 | Sistem harus membatasi akses data kendaraan hanya kepada pemilik terdaftar dan bengkel yang melakukan scan sah |
| SEC-010 | Sistem harus mencatat setiap aktivitas transfer kepemilikan dan verifikasi bengkel ke dalam audit log yang tidak dapat diubah (immutable) |
| SEC-011 | Sistem harus menerapkan session timeout otomatis untuk akun Admin Bengkel dan Super Admin setelah periode tidak aktif tertentu |
| SEC-012 | Sistem harus melakukan validasi dan sanitasi input pada seluruh form untuk mencegah SQL Injection dan XSS |


---

## 28. QR CODE WORKFLOW

1. **Generasi QR Code**: Saat kendaraan baru berhasil didaftarkan, sistem men-generate token unik terenkripsi yang dikaitkan dengan `vehicle_id`. Token ini dikodekan ke dalam QR Code.
2. **Aktivasi**: QR Code berstatus "Aktif" segera setelah dibuat dan siap digunakan oleh bengkel mitra mana pun yang terverifikasi.
3. **Distribusi Fisik**: Pengguna mengunduh gambar QR Code (format PNG/PDF) untuk dicetak dan ditempelkan secara fisik pada kendaraan.
4. **Pemindaian oleh Bengkel**: Pegawai bengkel memindai QR Code menggunakan kamera perangkat pada fitur Scan QR.
5. **Resolusi Token**: Sistem backend memvalidasi token QR Code terhadap database; jika valid, sistem mengembalikan data kendaraan terkait.
6. **Pencatatan Log Scan**: Setiap pemindaian dicatat (siapa yang memindai, bengkel mana, waktu pemindaian) untuk keperluan audit dan keamanan.
7. **Penanganan QR Code Tidak Valid**: Jika token tidak ditemukan/rusak/kedaluwarsa, sistem menampilkan pesan error dan mencatat percobaan tersebut sebagai potensi anomali.
8. **Regenerasi QR Code**: Apabila QR Code fisik hilang/rusak, pemilik kendaraan dapat mengajukan permintaan regenerasi melalui halaman Detail Kendaraan; QR Code lama otomatis dinonaktifkan begitu QR Code baru diterbitkan.

---

## 29. TRANSFER OWNERSHIP WORKFLOW

1. Pemilik lama membuka halaman Transfer Kepemilikan pada kendaraan yang akan dialihkan.
2. Sistem menampilkan ringkasan data kendaraan beserta riwayat service yang akan ikut berpindah.
3. Pemilik lama memasukkan data kontak (email/no HP) calon penerima.
4. Sistem memeriksa status akun penerima:
   - Jika penerima sudah memiliki akun Maintify → sistem mengirim permintaan persetujuan transfer ke akun tersebut.
   - Jika penerima belum memiliki akun → sistem mengirimkan undangan pendaftaran; proses transfer berstatus "Menunggu Pendaftaran Penerima".
5. Penerima login dan meninjau detail kendaraan yang akan diterimanya, lalu menyetujui atau menolak.
6. Jika disetujui, sistem menampilkan halaman **Review** kepada pemilik lama, mencakup: data kendaraan, data penerima, dan **disclaimer** bahwa proses ini bersifat permanen.
7. Pemilik lama memberikan konfirmasi final.
8. Sistem mengeksekusi transfer secara atomik (transactional):
   - Kepemilikan `vehicle_id` dialihkan ke `user_id` penerima.
   - QR Code kendaraan tetap sama (riwayat tidak berubah, hanya kepemilikan yang berpindah).
   - Seluruh `service_records` tetap melekat pada kendaraan (tidak terhapus).
   - Akses pemilik lama terhadap kendaraan tersebut dicabut.
9. Sistem menampilkan halaman **Transfer Berhasil** kepada pemilik lama, serta mengirimkan notifikasi konfirmasi ke kedua pihak.
10. Status transfer tercatat permanen dalam `ownership_transfers` dan `audit_logs` sebagai jejak riwayat kepemilikan kendaraan.

---

## 30. WORKSHOP VERIFICATION WORKFLOW

1. Calon bengkel mitra mengisi form Registrasi Bengkel, termasuk mengunggah dokumen legalitas (misalnya NIB/SIUP, KTP pemilik).
2. Sistem menyimpan pengajuan dengan status **"Menunggu Verifikasi"**.
3. Super Admin menerima notifikasi adanya pengajuan baru pada Dashboard Sistem.
4. Super Admin meninjau kelengkapan dokumen dan kevalidan data (nama bengkel, alamat, kontak, dokumen legal).
5. Super Admin memilih salah satu tindakan:
   - **Setujui** → status bengkel berubah menjadi "Terverifikasi", akun Admin Bengkel diaktifkan, dan bengkel dapat mulai login serta menambahkan pegawai.
   - **Tolak** → Super Admin wajib mengisi alasan penolakan, status berubah menjadi "Ditolak", dan bengkel menerima notifikasi beserta alasan agar dapat mengajukan ulang.
   - **Minta Informasi Tambahan** → status berubah menjadi "Perlu Revisi", bengkel dapat melengkapi data tanpa perlu mendaftar ulang dari awal.
6. Seluruh keputusan verifikasi tercatat dalam `audit_logs` beserta identitas Super Admin yang memprosesnya.

---

## 31. NOTIFICATION FLOW

| Jenis Notifikasi | Trigger | Penerima | Channel |
|---|---|---|---|
| Pengingat Service (Waktu) | Interval waktu sejak service terakhir terlampaui ambang batas | Pelanggan | In-app, Email |
| Pengingat Service (Jarak) | Odometer mendekati/melampaui ambang batas km | Pelanggan | In-app, Email |
| Riwayat Service Baru | Bengkel menambahkan riwayat service baru | Pelanggan | In-app, Email |
| Permintaan Transfer Kepemilikan | Pemilik lama menginisiasi transfer | Penerima | In-app, Email |
| Persetujuan/Penolakan Transfer | Penerima merespons permintaan transfer | Pemilik lama | In-app, Email |
| Transfer Berhasil | Transfer selesai dieksekusi | Pemilik lama & Penerima | In-app, Email |
| Status Verifikasi Bengkel | Super Admin memproses pengajuan bengkel | Calon Mitra Bengkel | In-app, Email |
| Akun Pegawai Ditambahkan | Admin Bengkel menambahkan pegawai baru | Pegawai Bengkel | In-app, Email |
| Peringatan Keamanan | Percobaan login gagal berulang / aktivitas mencurigakan | Pengguna terkait, Super Admin | In-app, Email |

---

## 32. DASHBOARD REQUIREMENT

### 32.1 Dashboard Pelanggan

- Ringkasan kendaraan utama (Hero Vehicle Card).
- Statistik jumlah kendaraan dan status kesehatan rata-rata.
- Riwayat service terakhir.
- Akses cepat ke QR Code / Digital ID.
- Notifikasi pengingat service terdekat.

### 32.2 Dashboard Admin Bengkel

- Jumlah service harian, mingguan, bulanan.
- Jumlah pegawai aktif.
- Grafik tren jumlah kendaraan yang dilayani.
- Ringkasan sparepart yang paling sering digunakan.
- Akses cepat ke laporan operasional.

### 32.3 Dashboard Super Admin

- Total pengguna terdaftar (per role).
- Total bengkel mitra (terverifikasi/menunggu/ditolak).
- Total kendaraan terdaftar dalam sistem.
- Grafik pertumbuhan platform (registrasi baru per periode).
- Status kesehatan sistem (uptime, error rate, jumlah request API).
- Daftar pengajuan verifikasi bengkel yang menunggu tindakan.

---

## 33. ROLE PERMISSION MATRIX

| Modul/Fitur | Pelanggan | Pegawai Bengkel | Admin Bengkel | Super Admin |
|---|---|---|---|---|
| Login/Register | ✅ | ✅ (dibuat Admin Bengkel) | ✅ | ✅ (+OTP) |
| Kelola Profil Sendiri | ✅ | ✅ | ✅ | ✅ |
| Tambah/Kelola Kendaraan Sendiri | ✅ | ❌ | ❌ | ❌ (view only) |
| Lihat QR Code Kendaraan | ✅ (miliknya) | ✅ (saat scan) | ❌ | ✅ (semua) |
| Scan QR Kendaraan | ❌ | ✅ | ❌ | ❌ |
| Tambah/Ubah Riwayat Service | ❌ | ✅ | ✅ (koreksi) | ✅ (override) |
| Kelola Sparepart | ❌ | ✅ | ✅ | ✅ |
| Cari Bengkel Terdekat | ✅ | ❌ | ❌ | ❌ |
| Transfer Kepemilikan | ✅ | ❌ | ❌ | ✅ (support/audit) |
| Kelola Pegawai Bengkel | ❌ | ❌ | ✅ | ✅ |
| Kelola Data Bengkel Sendiri | ❌ | ❌ | ✅ | ✅ |
| Laporan Operasional Bengkel | ❌ | ❌ | ✅ | ✅ |
| Verifikasi Bengkel Baru | ❌ | ❌ | ❌ | ✅ |
| Kelola Seluruh Pengguna | ❌ | ❌ | ❌ | ✅ |
| Kelola Seluruh Bengkel | ❌ | ❌ | ❌ | ✅ |
| Kelola Seluruh Kendaraan | ❌ | ❌ | ❌ | ✅ |
| Monitoring Sistem | ❌ | ❌ | ❌ | ✅ |
| Pengaturan Global | ❌ | ❌ | ❌ | ✅ |
| Audit Log | ❌ | ❌ | ❌ | ✅ |


---

## 34. ERROR HANDLING

| Kode | Skenario | Pesan yang Ditampilkan | Tindakan Sistem |
|---|---|---|---|
| ERR-401 | Sesi login tidak valid/kedaluwarsa | "Sesi Anda telah berakhir, silakan login kembali" | Redirect ke halaman Login |
| ERR-403 | Pengguna mencoba mengakses fitur di luar hak aksesnya | "Anda tidak memiliki akses ke halaman ini" | Redirect ke Dashboard sesuai role |
| ERR-404 | Data (kendaraan/bengkel/service) tidak ditemukan | "Data tidak ditemukan" | Tampilkan halaman/empty state |
| ERR-409 | Duplikasi data (VIN/Plat Nomor/Email) | "Data sudah terdaftar sebelumnya" | Batalkan proses simpan, minta koreksi input |
| ERR-422 | Validasi input gagal | "Mohon periksa kembali data yang Anda masukkan" | Highlight field yang bermasalah |
| ERR-429 | Terlalu banyak percobaan (login/scan) | "Terlalu banyak percobaan, silakan coba lagi dalam beberapa menit" | Terapkan cooldown sementara |
| ERR-500 | Kesalahan server internal | "Terjadi kesalahan pada sistem, silakan coba lagi" | Log error untuk tim teknis, tampilkan fallback UI |
| ERR-QR01 | QR Code tidak valid/tidak terdaftar | "QR Code tidak dikenali oleh sistem" | Minta pemindaian ulang |
| ERR-QR02 | QR Code sudah dinonaktifkan (misal karena regenerasi) | "QR Code ini sudah tidak aktif" | Arahkan untuk menghubungi pemilik/cek QR terbaru |
| ERR-TRF01 | Penerima transfer menolak permintaan | "Permintaan transfer ditolak oleh penerima" | Batalkan proses transfer, notifikasi ke pemilik lama |
| ERR-NET01 | Koneksi internet terputus saat submit form | "Koneksi terputus, data belum tersimpan" | Simpan draft sementara di sisi klien bila memungkinkan |

---

## 35. VALIDATION RULES

| Field | Aturan Validasi |
|---|---|
| Email | Format email valid, unik dalam sistem |
| Password | Minimal 8 karakter, kombinasi huruf dan angka |
| Konfirmasi Password | Harus sama persis dengan Password |
| Nomor HP | Format numerik, 10–14 digit, diawali dengan kode negara/awalan yang valid |
| VIN Kendaraan | Unik dalam sistem, format alfanumerik sesuai standar VIN (17 karakter) |
| Plat Nomor | Unik dalam sistem, format sesuai standar plat nomor Indonesia |
| Odometer | Nilai numerik, tidak boleh lebih kecil dari odometer tercatat sebelumnya |
| Tahun Kendaraan | Nilai numerik, tidak boleh melebihi tahun berjalan |
| Username Super Admin | Unik, minimal 5 karakter |
| OTP | 6 digit numerik, berlaku maksimal 5 menit |
| Data Bengkel (Registrasi) | Nama bengkel, alamat, dan dokumen legalitas wajib diisi/diunggah |
| Upload Foto Kendaraan | Format JPG/PNG, ukuran maksimal 5MB |
| Upload Dokumen Bengkel | Format PDF/JPG/PNG, ukuran maksimal 10MB |

---

## 36. EMPTY STATE

| Halaman | Kondisi Kosong | Tampilan yang Disarankan |
|---|---|---|
| My Vehicles | Belum ada kendaraan terdaftar | Ilustrasi kendaraan + teks "Anda belum memiliki kendaraan terdaftar" + tombol "Tambah Kendaraan" |
| Riwayat Service | Belum ada riwayat service | Ilustrasi + teks "Belum ada riwayat service untuk kendaraan ini" |
| Bengkel Terdekat | Tidak ada bengkel dalam radius pencarian | Teks "Tidak ditemukan bengkel mitra di sekitar lokasi Anda" + saran memperluas radius |
| Daftar Pelanggan (Bengkel) | Belum ada pelanggan yang dilayani | Teks "Belum ada data pelanggan" |
| Kelola Pegawai | Belum ada pegawai terdaftar | Teks "Belum ada pegawai" + tombol "Tambah Pegawai" |
| Verifikasi Bengkel | Tidak ada pengajuan yang menunggu | Teks "Tidak ada pengajuan bengkel baru saat ini" |
| Notifikasi | Belum ada notifikasi | Teks "Belum ada notifikasi untuk Anda" |
| Audit Log | Belum ada aktivitas tercatat (kondisi awal sistem) | Teks "Belum ada aktivitas tercatat" |

## 37. LOADING STATE

- Seluruh proses pengambilan data (fetch dashboard, daftar kendaraan, riwayat service, hasil pencarian bengkel) harus menampilkan **skeleton loader** atau **spinner** yang konsisten dengan komponen UI terkait.
- Proses scan QR Code menampilkan indikator "Memindai..." selama kamera memproses gambar.
- Proses submit form (login, register, tambah kendaraan, transfer) harus menonaktifkan tombol submit dan menampilkan indikator loading untuk mencegah double submission.
- Proses generate QR Code menampilkan indikator "Membuat QR Code..." sebelum hasil ditampilkan.

## 38. SUCCESS STATE

| Aksi | Tampilan Success State |
|---|---|
| Registrasi Akun Berhasil | Notifikasi sukses + redirect ke Login/Dashboard |
| Kendaraan Berhasil Ditambahkan | Notifikasi sukses + redirect ke halaman QR Code kendaraan |
| Riwayat Service Berhasil Disimpan | Notifikasi sukses + tampilan riwayat terbaru pada timeline |
| Transfer Kepemilikan Berhasil | Halaman khusus "Transfer Berhasil" dengan detail lengkap |
| Bengkel Berhasil Diverifikasi | Notifikasi sukses ke Super Admin + status bengkel berubah menjadi Terverifikasi |
| Password Berhasil Diubah | Notifikasi sukses + opsi logout dari sesi lain |

## 39. FAILURE STATE

| Aksi | Tampilan Failure State |
|---|---|
| Login Gagal | Pesan error umum "Email atau password salah" tanpa detail spesifik (keamanan) |
| Registrasi Gagal (Email Terdaftar) | Pesan error spesifik pada field email |
| Scan QR Gagal | Pesan error "QR Code tidak valid" dengan opsi coba lagi |
| Transfer Ditolak Penerima | Notifikasi kegagalan transfer beserta alasan (jika diberikan) |
| Verifikasi Bengkel Ditolak | Notifikasi penolakan beserta alasan dari Super Admin |
| Upload Gagal (Ukuran/Format File) | Pesan error spesifik terkait format/ukuran file yang diizinkan |
| Koneksi Terputus | Pesan "Periksa koneksi internet Anda" dengan opsi retry |

## 40. EDGE CASES

| No | Skenario | Penanganan Sistem |
|---|---|---|
| 1 | Kendaraan diservis di dua bengkel berbeda pada hari yang sama | Sistem mencatat kedua entri riwayat secara terpisah dengan timestamp berbeda |
| 2 | QR Code dipindai oleh bengkel yang belum terverifikasi | Sistem menolak akses dan menampilkan pesan bahwa bengkel belum terverifikasi |
| 3 | Proses transfer kepemilikan terputus di tengah jalan (penerima belum merespons dalam waktu lama) | Sistem memberikan batas waktu (misal 7 hari), transfer otomatis dibatalkan jika kedaluwarsa |
| 4 | Pemilik lama mencoba membatalkan transfer setelah dikonfirmasi final | Sistem menolak, karena transfer final bersifat permanen (sesuai disclaimer) |
| 5 | Input odometer baru lebih kecil dari odometer sebelumnya | Sistem menampilkan validasi error dan meminta konfirmasi/koreksi dari pegawai bengkel |
| 6 | Bengkel mendaftar ulang setelah sebelumnya ditolak | Sistem memperbolehkan pengajuan baru dengan mereferensikan riwayat pengajuan sebelumnya |
| 7 | Pengguna kehilangan akses ke email terdaftar saat butuh reset password | Disediakan jalur pemulihan akun manual melalui verifikasi data tambahan oleh tim support |
| 8 | Dua Admin Bengkel mengedit data bengkel yang sama secara bersamaan | Sistem menerapkan mekanisme optimistic locking untuk mencegah data saling menimpa |
| 9 | QR Code fisik rusak/pudar dan tidak dapat dipindai | Pemilik dapat mengajukan regenerasi QR Code baru melalui halaman Detail Kendaraan |
| 10 | Kendaraan yang sudah ditransfer masih dicoba diakses oleh pemilik lama | Sistem menampilkan pesan akses ditolak karena kepemilikan telah berpindah |


---

## 41. FUTURE ENHANCEMENT

- Pengembangan aplikasi mobile native (iOS & Android) untuk pengalaman pengguna yang lebih optimal, termasuk push notification.
- Integrasi dengan sistem OBD (On-Board Diagnostics) untuk monitoring kondisi mesin secara real-time.
- Fitur marketplace jual-beli kendaraan bekas terintegrasi dengan riwayat service terverifikasi Maintify.
- Integrasi dengan platform asuransi kendaraan untuk penyesuaian premi berdasarkan riwayat perawatan.
- Fitur e-wallet/pembayaran terintegrasi untuk transaksi service langsung di dalam aplikasi.
- Sistem rating dan ulasan bengkel oleh pelanggan.
- Program loyalitas (loyalty points) bagi pelanggan aktif.
- Multi-bahasa penuh (Inggris dan bahasa daerah tertentu).
- Fitur chat langsung antara pelanggan dan bengkel mitra.
- Analitik prediktif untuk memperkirakan kebutuhan sparepart berdasarkan pola histori kendaraan.
- Integrasi API dengan Samsat/instansi terkait untuk validasi data kendaraan resmi.

---

## 42. KPI PRODUK

| KPI | Target Indikatif | Metode Pengukuran |
|---|---|---|
| Jumlah pengguna terdaftar (Pelanggan) | 10.000 pengguna dalam 6 bulan pertama | Data registrasi sistem |
| Jumlah bengkel mitra terverifikasi | 200 bengkel dalam 6 bulan pertama | Data verifikasi Super Admin |
| Rata-rata jumlah scan QR per bengkel per bulan | Minimal 50 scan/bengkel/bulan | Log pemindaian QR Code |
| Tingkat retensi pengguna bulanan (MAU/Registered) | Minimal 40% | Analitik penggunaan sistem |
| Waktu rata-rata proses pencatatan service oleh bengkel | Di bawah 3 menit per transaksi | Timestamp mulai-selesai input form |
| Tingkat keberhasilan transfer kepemilikan | Minimal 90% transfer selesai tanpa kendala teknis | Data status ownership_transfers |
| Uptime sistem | Minimal 99.5% per bulan | Monitoring infrastruktur |
| Net Promoter Score (NPS) Pelanggan | Minimal skor 40 | Survei berkala kepada pengguna |
| Tingkat kepuasan Admin Bengkel terhadap sistem laporan | Minimal 80% puas | Survei berkala kepada mitra bengkel |

---

## 43. RISK ANALYSIS

| No | Risiko | Kategori | Dampak | Mitigasi |
|---|---|---|---|---|
| 1 | Bengkel fiktif mendaftar untuk mengakses data pelanggan | Keamanan | Tinggi | Proses verifikasi manual ketat oleh Super Admin, validasi dokumen legalitas |
| 2 | QR Code dipalsukan/digandakan | Keamanan | Tinggi | Enkripsi token QR Code, log setiap pemindaian, deteksi anomali pola scan |
| 3 | Data odometer dimanipulasi untuk menaikkan nilai jual kendaraan | Integritas Data | Tinggi | Validasi input odometer (tidak boleh menurun), riwayat scan yang tercatat permanen |
| 4 | Kebocoran data pribadi pengguna | Keamanan/Legal | Sangat Tinggi | Enkripsi data, kepatuhan UU PDP, audit keamanan berkala |
| 5 | Rendahnya adopsi bengkel mitra pada tahap awal | Bisnis | Sedang | Program insentif onboarding bengkel, kemudahan proses pendaftaran |
| 6 | Ketergantungan pada koneksi internet saat proses scan di bengkel | Operasional | Sedang | Mekanisme offline-first dengan sinkronisasi otomatis saat koneksi kembali tersedia (roadmap lanjutan) |
| 7 | Kompleksitas proses transfer kepemilikan menyebabkan kebingungan pengguna | UX | Sedang | Desain flow yang jelas dengan halaman review dan disclaimer eksplisit |
| 8 | Skalabilitas sistem saat pertumbuhan pengguna melonjak cepat | Teknis | Tinggi | Arsitektur cloud-native yang scalable, load testing berkala |
| 9 | Resistensi bengkel konvensional terhadap perubahan proses digital | Adopsi | Sedang | Pelatihan onboarding, antarmuka yang sederhana dan intuitif bagi pegawai bengkel |

---

## 44. TECHNICAL RECOMMENDATION

- Menggunakan arsitektur **microservices atau modular monolith** pada tahap awal untuk mempercepat pengembangan MVP, dengan kemungkinan migrasi ke microservices penuh seiring pertumbuhan skala pengguna.
- Menerapkan **API Gateway** sebagai titik masuk tunggal untuk seluruh permintaan dari frontend, guna mempermudah penerapan rate limiting, autentikasi, dan monitoring.
- Menggunakan **cloud infrastructure** (contoh: AWS/GCP/Azure) untuk mendukung skalabilitas dan ketersediaan tinggi.
- Menerapkan **CI/CD pipeline** untuk mempercepat siklus deployment dan menjaga kualitas kode melalui automated testing.
- Menggunakan **object storage** (contoh: S3-compatible) untuk penyimpanan file (foto kendaraan, dokumen bengkel, QR Code).
- Menerapkan **caching layer** (contoh: Redis) untuk mempercepat query yang sering diakses seperti pencarian bengkel terdekat.
- Menggunakan **message queue** (contoh: RabbitMQ/SQS) untuk memproses notifikasi dan pengingat service secara asinkron agar tidak membebani proses utama.
- Menerapkan **geospatial indexing** (contoh: PostGIS) untuk mendukung fitur pencarian bengkel berbasis lokasi secara efisien.

---

## 45. TECH STACK RECOMMENDATION

| Layer | Rekomendasi Teknologi |
|---|---|
| Frontend | React.js / Next.js dengan TypeScript, TailwindCSS untuk styling responsif |
| Backend | Node.js (NestJS/Express) atau alternatif seperti Laravel/Django sesuai keahlian tim |
| Database | PostgreSQL (dengan ekstensi PostGIS untuk fitur geolokasi) |
| Cache | Redis |
| File/Object Storage | Amazon S3 atau layanan setara |
| Autentikasi | JWT dengan refresh token, OTP menggunakan layanan pihak ketiga (contoh: Twilio/WhatsApp API) |
| Notifikasi | Email (SMTP/SendGrid), Push/In-app Notification |
| QR Code Generator | Library QR Code standar (contoh: qrcode.js pada backend Node.js) dengan token terenkripsi |
| Peta & Geolokasi | Google Maps API / Mapbox |
| Hosting/Infrastruktur | AWS / GCP / Azure dengan container orchestration (Docker + Kubernetes untuk skala lanjutan) |
| CI/CD | GitHub Actions / GitLab CI |
| Monitoring | Grafana + Prometheus, Sentry untuk error tracking |

---

## 46. MILESTONE DEVELOPMENT

| Milestone | Cakupan | Estimasi Durasi |
|---|---|---|
| M1: Foundation & Autentikasi | Setup infrastruktur, database, modul Login/Register (seluruh role), RBAC dasar | 3 minggu |
| M2: Modul Kendaraan & QR Code | Tambah kendaraan, generate QR Code, detail kendaraan, dashboard pelanggan | 3 minggu |
| M3: Modul Bengkel & Service | Scan QR, tambah riwayat service, kelola sparepart, dashboard pegawai bengkel | 4 minggu |
| M4: Modul Admin Bengkel | Dashboard admin bengkel, kelola pegawai, kelola pelanggan, laporan | 3 minggu |
| M5: Modul Pencarian Bengkel & Notifikasi | Bengkel terdekat (peta), sistem pengingat service, notifikasi | 3 minggu |
| M6: Modul Transfer Kepemilikan | Flow transfer lengkap, halaman review, transfer berhasil | 2 minggu |
| M7: Modul Super Admin | Verifikasi bengkel, kelola pengguna, audit log, monitoring sistem | 3 minggu |
| M8: QA, Security Hardening & UAT | Pengujian menyeluruh, perbaikan bug, penetration testing dasar, User Acceptance Test | 3 minggu |
| M9: Deployment & Go-Live | Deployment produksi, monitoring awal pasca-launch | 1 minggu |

## 47. TIMELINE PENGEMBANGAN

Total estimasi durasi pengembangan MVP: **±25 minggu (kurang lebih 6 bulan)**, dengan asumsi tim inti terdiri dari 1 Product Manager, 1 UI/UX Designer, 2 Frontend Developer, 2 Backend Developer, 1 QA Engineer, dan 1 DevOps Engineer, bekerja secara paralel antar milestone yang memungkinkan (contoh: desain UI/UX untuk M3 dapat dimulai saat development M2 masih berjalan).

Rincian timeline disusun lebih lanjut secara terpisah pada dokumen **`plan.md`** dalam bentuk task breakdown per sprint.

---

## 48. KESIMPULAN

Maintify (SERVTRACK) hadir sebagai jawaban atas permasalahan mendasar dalam pengelolaan riwayat service kendaraan bermotor di Indonesia yang selama ini bersifat manual, tersebar, dan rawan manipulasi. Dengan memanfaatkan teknologi QR Code sebagai identitas digital kendaraan — terinspirasi dari keberhasilan implementasi serupa pada program subsidi BBM nasional — Maintify menawarkan satu sumber data yang terpusat, real-time, dan terverifikasi bagi seluruh pemangku kepentingan: pemilik kendaraan, bengkel mitra, dan pengelola platform.

Dokumen PRD ini telah menjabarkan secara menyeluruh mulai dari latar belakang, target pengguna, persona, alur bisnis, kebutuhan fungsional dan non-fungsional, hingga rekomendasi teknis dan rencana pengembangan bertahap. Dengan struktur multi-role yang jelas (Pelanggan, Pegawai Bengkel, Admin Bengkel, Super Admin) serta fitur-fitur inti seperti pencatatan service berbasis scan QR, dashboard monitoring kendaraan, pencarian bengkel terdekat, dan transfer kepemilikan yang aman, Maintify dirancang untuk menjadi platform yang scalable, aman, dan memberikan nilai nyata bagi ekosistem otomotif di Indonesia.

Dokumen ini menjadi acuan utama bagi tim UI/UX, Frontend, Backend, QA, dan DevOps dalam merancang dan membangun Maintify secara terstruktur, konsisten, dan sesuai dengan visi produk yang telah ditetapkan. Dua dokumen pendamping — **`task-list.md`** (rincian task pengembangan) dan **`erd.md`** (struktur database detail) serta **`plan.md`** (rencana eksekusi/sprint) — disusun untuk melengkapi PRD ini sebagai satu kesatuan paket dokumentasi pengembangan produk.

---

*Dokumen ini bersifat living document dan dapat diperbarui seiring dengan perkembangan kebutuhan bisnis dan hasil validasi pengguna selama proses pengembangan berlangsung.*
