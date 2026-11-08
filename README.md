# SIMAS-RS (Sistem Informasi Manajemen Aset Rumah Sakit)
### Rumah Sakit UMMI Bogor

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v3%2Fv5-F59E0B?logo=filament&logoColor=white)](https://filamentphp.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/Database-MySQL_8.0%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tests](https://img.shields.io/badge/Tests-42_Passed-success?logo=github-actions&logoColor=white)](https://phpunit.de/)

**SIMAS-RS** adalah aplikasi manajemen inventaris dan fasilitas rumah sakit berbasis web yang dirancang khusus untuk operasional **Rumah Sakit UMMI Bogor**. Sistem ini mengelola siklus hidup lengkap seluruh aset rumah sakit—mulai dari registrasi aset, cetak stiker barcode QR, pelaporan kerusakan alat (*work order*), pemeliharaan preventif, pencatatan kalibrasi alat medis wajib tera (BPFK), mutasi ruangan, hingga audit *stock opname* fisik persediaan.

---

## 1. Fitur Utama

### A. Role-Based Access Control (RBAC) 6 Tingkatan
Sistem menerapkan pembatasan hak akses yang ketat dan terenkripsi sesuai tanggung jawab operasional masing-masing peran:
1. **Administrator Sistem (`admin`):**
   - Akses menyeluruh (*full control*) terhadap sistem, konfigurasi master data, dan audit log.
   - Manajemen akun staf, aktivasi/nonaktivasi pengguna, dan penugasan peran (*Role Matrix*).
   - Cetak label QR code dan monitoring seluruh operasional rumah sakit.
2. **Petugas Aset (`petugas_aset`):**
   - Registrasi aset baru dengan penomoran kode otomatis `AST-YYYY-NNNNNN`.
   - Pengambilan foto barang langsung via webcam/kamera smartphone.
   - Cetak label stiker barcode QR (tunggal & lembar batch A4 isi 15 label).
   - Pengajuan mutasi ruangan dan pelaksanaan audit fisik *stock opname*.
3. **Teknisi IPSRS & Biomedis (`teknisi`):**
   - Penanganan cepat tiket perbaikan (*Work Order*) dengan respon awal $\le 30$ menit untuk prioritas tinggi.
   - Pembaruan status tiket: `Menunggu` $\rightarrow$ `Dikerjakan` $\rightarrow$ `Menunggu Suku Cadang` $\rightarrow$ `Selesai`.
   - Pelaksanaan jadwal pemeliharaan preventif rutin (*Preventive Maintenance*).
   - Pencatatan sertifikat tera uji kelaikan alat medis dari Balai Pengujian Fasilitas Kesehatan (BPFK).
4. **Kepala Unit / Ruangan (`kepala_unit`):**
   - Pengawasan daftar inventaris alat yang dialokasikan khusus di unit kerja ruangan terkait.
   - Otorisasi perpindahan alat masuk melalui mekanisme persetujuan (*Approve* / *Reject*).
   - Pelaporan kerusakan alat medis/fasilitas dan konfirmasi serah terima hasil perbaikan.
5. **Staf Klinis / Perawat (`staf`):**
   - Pemindaian cepat stiker QR code fisik alat via kamera bawaan HP tanpa perlu aplikasi tambahan.
   - Pelaporan cepat tiket *work order* saat terjadi kendala alat di *Nurse Station*.
   - Pemantauan status penanganan perbaikan secara *real-time*.
6. **Manajemen & Direksi (`manajemen`):**
   - *Executive Dashboard* pemantauan rasio kesiapan alat penunjang hidup (*Life Support Readiness Rate* target $\ge 90\%$).
   - Pengawasan kepatuhan kalibrasi alat medis yang mendekati masa kedaluwarsa ($\le 30$ hari) untuk akreditasi rumah sakit.
   - Peringatan dini persediaan barang di bawah batas minimum (*Safety Stock*).
   - Evaluasi laporan finansial nilai aset dan analisis nominal selisih varians *stock opname*.

---

### B. Modul Operasional Unggulan
- **Penomoran Otomatis Terstandarisasi:**
  - Kode Aset: `AST-YYYY-NNNNNN`
  - Nomor Mutasi: `MUT-YYYY-NNNNNN`
  - Nomor Stock Opname: `OPN-YYYY-NNNNNN`
  - Nomor Tiket Work Order: `WO-YYYY-NNNNNN`
- **Pencetakan Label QR Code Presisi:**
  - Fitur cetak label dibatasi secara ketat hanya dapat diakses oleh **Administrator** dan **Petugas Aset** (peran lain mendapatkan respon 403 Forbidden).
  - Pilihan cetak: Stiker thermal tunggal atau lembar kertas stiker A4 (tata letak 3 kolom $\times$ 5 baris).
- **Integrasi Kamera Langsung:**
  - Petugas dapat memotret fisik barang secara *real-time* melalui webcam PC atau kamera ponsel langsung ke penyimpanan berkas aplikasi.
- **Audit Stock Opname & Analisis Varians:**
  - Perhitungan selisih antara data tercatat di sistem vs hitungan fisik riil di ruangan secara otomatis beserta total nominal kerugian finansial.

---

## 2. Tech Stack

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Bahasa Pemrograman** | PHP 8.3+ | Fitur tipe data ketat (*strict types*), modern OOP, performa tinggi |
| **Framework Backend** | Laravel 12.x / 11.x | Arsitektur MVC, Eloquent ORM, migrasi skema basis data |
| **Panel Admin & UI Engine** | Filament v3 / v5 | Antarmuka responsif, modern, mendukung tema terang/gelap |
| **Komponen Reaktif** | Livewire 4 & Alpine.js | Interaksi UI dinamis dan instan tanpa *full page reload* |
| **Framework CSS** | Tailwind CSS | Utility-first styling presisi tinggi |
| **Basis Data** | MySQL 8.0+ / MariaDB | Penyimpanan relasional dengan integritas kunci asing (*foreign keys*) |
| **Mesin PDF & Laporan** | Barryvdh Laravel-Dompdf | Pembangkit dokumen cetak A4 dan stiker label |
| **Mesin Barcode & QR** | Simple QrCode (BaconQrCode) | Pembangkitan kode QR beresolusi tinggi format SVG/PNG |
| **Manipulasi Spreadsheet** | PhpSpreadsheet | Ekspor dan impor data inventaris format Excel (.xlsx) |
| **Pengujian (Testing)** | PHPUnit 12 | Otomatisasi pengujian fitur dan kontrol akses peran |
| **Standar Kode (Linter)** | Laravel Pint | Pemformatan kode otomatis sesuai standar PSR-12 |

---

## 3. Struktur Proyek

```plaintext
simas-rs-ummi-seleksi/
├── app/
│   ├── Enums/                     # Enum status aset, kondisi, urgensi, dan log perbaikan
│   ├── Filament/
│   │   ├── Pages/                 # Halaman khusus Filament (Cetak Label QR batch)
│   │   ├── Resources/             # Sumber daya CRUD Filament (Aset, Mutasi, WO, Opname, Master)
│   │   └── Widgets/               # Widget analitik dasbor (Kesiapan alat medis, stok menipis)
│   ├── Http/
│   │   ├── Controllers/           # Controller pencetakan label QR & rute kustom
│   │   └── Requests/              # Validasi form request
│   ├── Models/                    # Model Eloquent (Asset, Movement, WorkOrder, StockOpname, User, Role)
│   └── Providers/
│       └── Filament/
│           └── AdminPanelProvider.php  # Konfigurasi panel navigasi, tema, dan middleware
├── database/
│   ├── factories/                 # Factory data dummy pengujian
│   ├── migrations/                # Skema migrasi tabel basis data
│   └── seeders/                   # Seeder data master (Departemen, Peran, Akun Resmi, Aset Medis)
├── public/
│   └── assets/                    # Logo resmi RS UMMI Bogor dan aset gambar publik
├── resources/
│   └── views/
│       ├── assets/                # Template cetak stiker barcode QR (single & batch A4)
│       └── filament/              # Kustomisasi tampilan halaman Filament
├── routes/
│   └── web.php                    # Definisi rute web dan proteksi autentikasi
├── tests/
│   ├── Feature/                   # Pengujian integrasi (RoleAccessTest, AssetQrPrintTest)
│   └── TestCase.php               # Konfigurasi dasar PHPUnit
├── composer.json                  # Manajemen dependensi PHP
├── package.json                   # Manajemen dependensi frontend (Tailwind/Vite)
└── phpunit.xml                    # Konfigurasi lingkungan pengujian otomatis
```

---

## 4. Cara Git Clone & Panduan Instalasi

### Prasyarat Sistem
Pastikan perangkat Anda telah terpasang:
- **PHP** versi 8.3 atau lebih baru (dengan ekstensi `pdo_mysql`, `mbstring`, `gd`, `zip`, `xml`, `curl`).
- **Composer** (manajemen dependensi PHP).
- **MySQL** versi 8.0+ atau **MariaDB** 10.4+.
- **Node.js** (v18+) & **npm** (opsional untuk kompilasi aset).
- **Git**.

---

### Langkah-Langkah Instalasi

#### 1. Klon Repositori
Buka terminal / PowerShell dan jalankan:
```bash
git clone https://github.com/username/simas-rs-ummi-seleksi.git
cd simas-rs-ummi-seleksi
```

#### 2. Pasang Dependensi PHP
```bash
composer install
```

#### 3. Konfigurasi Lingkungan (`.env`)
Salin berkas `.env.example` menjadi `.env`:
```bash
# Windows PowerShell
copy .env.example .env

# Linux / macOS
cp .env.example .env
```

Buka berkas `.env` dan sesuaikan koneksi basis data Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simas_rs_ummi_seleksi
DB_USERNAME=root
DB_PASSWORD=
```

#### 4. Buat Application Key
```bash
php artisan key:generate
```

#### 5. Jalankan Migrasi & Seeder Basis Data
Jalankan migrasi skema basis data beserta data awal resmi (Departemen, Peran, Ruangan, Akun, dan Contoh Aset):
```bash
php artisan migrate --seed
```

#### 6. Hubungkan Storage Simbolik
```bash
php artisan storage:link
```

#### 7. Jalankan Server Pengembangan
```bash
php artisan serve
```
Aplikasi kini dapat diakses melalui peramban di: **`http://127.0.0.1:8000`** (otomatis diarahkan ke portal login `/admin/login`).

---

## 5. Kredensial Akun Bawaan (Default Login)

Seluruh akun awal menggunakan kata sandi standar: **`password`**

| Peran Sistem | Alamat Email | Kata Sandi | Unit Penugasan | Hak Akses Utama |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | `admin@rs-ummi.id` | `password` | IPSRS | Penuh (*Full Access* Data Master, Pengguna, Cetak QR) |
| **Petugas Aset** | `petugas.aset@rs-ummi.id` | `password` | IPSRS | Inventaris Aset, Cetak Label QR, Mutasi, Stock Opname |
| **Teknisi IPSRS** | `teknisi@rs-ummi.id` | `password` | IPSRS | Tiket Work Order, Pemeliharaan Preventif, Kalibrasi |
| **Kepala Unit** | `kepala.unit@rs-ummi.id` | `password` | ICU | Monitoring Aset Ruangan, Persetujuan Mutasi Masuk |
| **Staf Klinis** | `staf.klinis@rs-ummi.id` | `password` | ICU | Scan QR Cepat, Pelaporan Kerusakan Alat |
| **Manajemen** | `manajemen@rs-ummi.id` | `password` | Direksi | Dasbor Eksekutif, Monitoring Kalibrasi & Finansial |

---

## 6. Menjalankan Pengujian Otomatis

Aplikasi dilengkapi dengan rangkaian pengujian fitur (*feature testing*) untuk memastikan integritas logika bisnis dan keamanan pembatasan hak akses:
```bash
php artisan test
```
*Status Terkini: **42 tests passed, 269 assertions (100% lulus)**.*

---

## 7. Lisensi

Aplikasi ini dikembangkan untuk keperluan operasional **Rumah Sakit UMMI Bogor** di bawah lisensi [MIT License](LICENSE).
