# SIMAS-RS (Sistem Manajemen Aset Rumah Sakit) - Panduan untuk AI Agent

Versi dokumen: 0.3 (Revisi Final Pengguna - 6 November 2026) | Sumber: PRD & Kesepakatan Sesi

> [!IMPORTANT]
> **CATATAN REVISI KHUSUS DARI PENGGUNA (WAJIB DIPATUHI DI SELURUH SISTEM):**
> 1. **`AssetCondition`**: HANYA 2 pilihan: `baik` dan `rusak`.
> 2. **`AssetStatus`**: HANYA 4 pilihan: `aktif`, `dalam_perbaikan`, `non_aktif`, `hilang`. Status `dihapuskan` DIHILANGKAN.
> 3. **`MaintenanceLogStatus`**: HANYA 3 pilihan: `dijadwalkan`, `selesai`, `terlambat`. Status `dilewati` DIHILANGKAN.
> 4. **`WorkOrderStatus`**: HANYA 6 pilihan: `baru`, `ditugaskan`, `dikerjakan`, `menunggu_suku_cadang`, `selesai`, `dibatalkan`. Status `ditutup` DIHILANGKAN (status `selesai` adalah status akhir sukses).

Tanda yang dipakai:
- **[PRD]** = sudah tertulis di PRD
- **[Usulan]** = saran, belum disetujui
- **[Perlu keputusan]** = harus diputuskan sebelum coding

---

## 1. Deskripsi Proyek

- **Tujuan Produk:** Mencatat, melacak, merawat, dan melaporkan aset rumah sakit (alat medis, IT, gedung, furnitur) dalam satu aplikasi web yang responsif untuk HP. **[PRD]**
- **Alur inti:** catat → lacak → rawat → laporkan. **[PRD]**
- **Target Pengguna:** Administrator sistem, petugas aset, teknisi IPSRS/biomedis, kepala unit/ruangan, staf klinis, manajemen. **[PRD]**
- **Fitur Utama (MVP):** **[PRD]**
  - F1 Autentikasi dan RBAC (6 peran)
  - F2 Data master (kategori, lokasi, unit, vendor, merek/model)
  - F3 Registrasi aset, kode `AST-2026-000123`, label QR, pencarian, impor Excel
  - F4 Pelacakan lokasi dan mutasi (persetujuan opsional)
  - F5 Perawatan preventif dan pengingat
  - F6 Laporan kerusakan dan work order
  - F7 Kalibrasi dan masa berlaku sertifikat
  - F8 Stock opname berbasis scan QR
  - F9 Dashboard dan laporan (ekspor Excel/PDF)
  - F10 Audit log dan notifikasi
- **Di luar MVP:** integrasi SIMRS/akuntansi/e-procurement, penyusutan otomatis, manajemen suku cadang, RFID/IoT, aplikasi native, prediksi AI, WhatsApp. **[PRD]**
- **Larangan data:** Sistem TIDAK menyimpan data medis pasien. **[PRD]**

### Metrik Keberhasilan (3 bulan setelah go-live) **[PRD]**
- Aset kritis terdata ≥ 95%
- Perawatan preventif tepat waktu ≥ 85%
- Kalibrasi masih berlaku ≥ 95%
- Waktu laporan sampai teknisi menerima tugas ≤ 30 menit
- Waktu stock opname turun ≥ 50%
- Pengguna aktif mingguan ≥ 70%

---

## 2. Tech Stack & Arsitektur

- **Backend:** Laravel 13.40.0, PHP 8.3.20 **[Perlu keputusan: pastikan composer.json mengunci versi ini]**
- **Frontend:** Blade + **Bootstrap 5 + CoreUI v5.6.0** **[Sudah ditentukan]**
- **Aset frontend:** CSS, JS bundle, Chart.js, Simplebar, dan Icon SVG sudah berada di `public/`. Aplikasi memakai file ini langsung, **tanpa** proses build atau npm untuk CSS/JS.
- **Interaktivitas:** JavaScript vanilla dan komponen JS bawaan CoreUI/Bootstrap. Tidak memakai Alpine.js atau Tailwind.
- **Database:** MySQL 8 **[Perlu keputusan]**. PRD menulis PostgreSQL. Jika MySQL dipakai, kolom `JSONB` menjadi `JSON`, dan pencarian teks memakai `FULLTEXT`.
- **Autentikasi:** Laravel Fortify atau Breeze (bagian login saja, disesuaikan ke Bootstrap). Hash bcrypt/argon2. **[PRD]**
- **Otorisasi:** Laravel Policy + Gate berbasis peran. **[Usulan]**
- **Scan QR:** library scanner berbasis kamera browser. Disimpan di `public/js/vendor/` **[Perlu keputusan dan persetujuan instalasi]**
- **Chart dashboard:** Chart.js yang sudah ada di `public/`. **[PRD]**
- **Berkas (foto, sertifikat, faktur):** disk `s3`-compatible / MinIO. **[PRD]**
- **Antrean dan jadwal:** Laravel Scheduler (`routes/console.php`) + Queue. **[PRD: Cron/BullMQ, versi Laravel: Scheduler + Queue]**
- **Notifikasi:** in-app (tabel `notifications`) + email (Laravel Mail, dikirim lewat queue). **[PRD + Usulan]**
- **Ekspor:** Excel dan PDF. Library: `maatwebsite/excel` dan `barryvdh/laravel-dompdf`. **[Perlu keputusan dan persetujuan instalasi]**
- **Deployment:** Docker di server/VPS rumah sakit (on-premise). **[PRD, menunggu konfirmasi pertanyaan no. 6]**

---

## 3. Aturan Coding & Gaya Penulisan

- Ikuti **PSR-12** dan jalankan `vendor/bin/pint` sebelum menyerahkan kode.
- Setiap parameter dan return type wajib dideklarasikan. Hindari `mixed`.
- **Enum PHP 8.1+** untuk semua nilai status: `AssetStatus`, `AssetCondition`, `WorkOrderStatus`, `WorkOrderPriority`, `MaintenanceLogStatus`, `CalibrationResult`, `StockOpnameResult`, `NotificationChannel`.
- Logika bisnis di **Service class** (`app/Services`), bukan di Controller atau Model.
- Validasi input lewat **Form Request** (`app/Http/Requests`).
- Penamaan:
  - Class: `PascalCase` (contoh: `AssetMovementService.php`)
  - Method dan variabel: `camelCase`
  - Tabel: `snake_case` jamak (contoh: `asset_movements`)
  - View Blade: `kebab-case` (contoh: `resources/views/assets/show.blade.php`)
  - Komponen Blade: `resources/views/components/ui/` (contoh: `<x-ui.alert>`)
- Operasi yang mengubah lebih dari satu tabel **wajib** memakai `DB::transaction()`. **[PRD]**
- Setiap perubahan data sensitif wajib menulis `audit_logs`. **[PRD F10]**
- Pemanggilan eksternal (email, storage, library QR) wajib `try-catch` dan pesan error yang bisa dibaca pengguna.
- **Styling:** gunakan kelas Bootstrap 5 dan CoreUI (contoh: `btn btn-primary`, `alert alert-warning`, `badge text-bg-success`). **Jangan pakai inline style** dan jangan pakai Tailwind. Warna custom ditaruh di `public/css/simas.css`, dimuat setelah CoreUI. **[Usulan]**
- Teks antarmuka dan pesan error memakai **bahasa Indonesia**. **[PRD]**

---

## 4. Struktur Folder yang Harus Dipatuhi

```
app/
  Enums/                 → AssetStatus, AssetCondition, WorkOrderStatus, dst.
  Models/                → Asset, Category, Location, Department, User, ...
  Services/              → AssetService, MovementService, WorkOrderService,
                           MaintenanceService, CalibrationService, OpnameService,
                           ReminderService
  Http/Controllers/      → Tipis, hanya meneruskan ke Service
  Http/Requests/         → Validasi per form
  Policies/              → AssetPolicy, WorkOrderPolicy, dst.
  Jobs/                  → Pengiriman email pengingat, pembuatan jadwal
  Console/Commands/      → CheckCalibrationReminders, CheckMaintenanceDue
  Notifications/         → CalibrationReminderNotification, MaintenanceDueNotification,
                           WorkOrderAssignedNotification (channel: database + mail)
  Mail/                  → Mailable untuk email pengingat
  Support/               → Helper (generator kode aset, format tanggal WIB)
database/
  migrations/            → Berurutan sesuai bagian 10.5
  seeders/               → Roles, departments, kategori awal
resources/views/
  components/ui/         → alert.blade.php, toast.blade.php, badge-status.blade.php
  layouts/               → Layout CoreUI (sidebar, navbar)
  assets/, work-orders/, calibrations/, ...   → View per modul
  emails/                → Template email pengingat
routes/web.php           → Rute utama
routes/console.php       → Jadwal (Scheduler)
public/                  → CSS, JS, dan ikon CoreUI (jangan diubah)
public/css/simas.css     → Override warna dan gaya khusus proyek [Usulan]
```

---

## 5. Batasan Teknis & Larangan (Constraints)

- **JANGAN** mengubah `composer.json`, `package.json`, `.env`, atau file konfigurasi inti tanpa izin eksplisit.
- **JANGAN** mengubah file di `public/` yang berasal dari CoreUI atau Bootstrap. Jika perlu gaya khusus, tulis di `public/css/simas.css`.
- **JANGAN** hardcode kunci API, kata sandi, atau kredensial. Gunakan `env()` atau `config()`.
- **JANGAN** memasang package baru (composer maupun npm) tanpa menyebut alasannya dan mendapat konfirmasi.
- **JANGAN** menghapus aset atau pengguna secara permanen. Gunakan soft delete dan status. **[PRD]**
- **JANGAN** menyimpan data medis pasien. **[PRD]**
- **JANGAN** mengubah `location_id` aset langsung. Harus lewat `asset_movements`. **[PRD]**
- **JANGAN** mengubah status tiket langsung. Harus lewat `WorkOrderService` dan transisi yang diizinkan. **[Usulan]**
- **JANGAN** mengirim email pengingat langsung dari Controller. Harus lewat Queue. **[Usulan]**
- Sebelum menyerahkan kode, jalankan:
  - `php artisan test`
  - `vendor/bin/pint --test`
  - `php artisan migrate:fresh --seed` di lingkungan lokal

---

## 6. Alur Kerja yang Diharapkan

1. Baca file terkait yang sudah ada sebelum menulis kode baru.
2. Kerjakan secara modular. Jangan mengubah lebih dari 5 file dalam satu respons, dan ikuti urutan fase di bagian 13.
3. Setiap perubahan skema dibuat sebagai migration baru, bukan mengedit migration lama.
4. Setelah memberi kode, jelaskan logika dan bagian yang berubah dalam beberapa kalimat.
5. Jika ada informasi yang kurang, ajukan usulan dan tunggu persetujuan. **Jangan** mengisi sendiri.

---

## 7. Domain dan Istilah

| Istilah | Arti |
| --- | --- |
| Aset | Barang inventaris yang dicatat, punya kode unik |
| Mutasi | Perpindahan lokasi atau unit penanggung jawab aset |
| Preventif | Perawatan berkala yang dijadwalkan |
| Korektif | Perbaikan akibat kerusakan, dibuat lewat tiket (work order) |
| Kalibrasi | Pengujian berkala alat medis dengan masa berlaku sertifikat |
| Stock opname | Pengecekan fisik aset per ruangan dengan scan QR |
| IPSRS | Instalasi Pemeliharaan Sarana dan Rumah Sakit |
| Alat kritis | Alat life support (`is_critical = true`) |

---

## 8. Peran, Hak Akses (RBAC), dan Use Case

### 8.1 Peran **[PRD]**

| Peran (`roles.name`) | Kebutuhan utama |
| --- | --- |
| `admin` | Kelola pengguna, peran, data master, lihat audit log |
| `petugas_aset` | Input aset, mutasi, opname, laporan |
| `teknisi` | Terima tugas, perawatan, kalibrasi, perbaikan |
| `kepala_unit` | Lihat aset di unitnya, lapor kerusakan, setujui mutasi, konfirmasi tiket |
| `staf` | Scan QR, lapor kerusakan cepat |
| `manajemen` | Dashboard dan laporan (baca saja) |

Kepala Unit dan Staf hanya melihat aset di unitnya, kecuali ditentukan lain. **[PRD]**

### 8.2 Daftar Use Case

| ID | Use case | Aktor utama |
| --- | --- | --- |
| UC-01 | Login | Semua |
| UC-02 | Kelola pengguna dan peran | Admin |
| UC-03 | Kelola data master | Admin |
| UC-04 | Lihat audit log | Admin |
| UC-05 | Tambah aset dan cetak QR | Petugas aset |
| UC-06 | Impor aset dari Excel | Petugas aset |
| UC-07 | Cari dan lihat aset | Petugas aset, teknisi |
| UC-08 | Mutasi aset | Petugas aset |
| UC-09 | Stock opname | Petugas aset |
| UC-10 | Ekspor laporan | Petugas aset, manajemen, kepala unit |
| UC-11 | Scan QR dan lihat detail aset | Staf, kepala unit, teknisi |
| UC-12 | Lapor kerusakan | Staf, kepala unit |
| UC-13 | Pantau status tiket | Pelapor |
| UC-14 | Setujui atau tolak mutasi | Kepala unit tujuan (jika aktif) |
| UC-15 | Konfirmasi perbaikan selesai | Pelapor atau kepala unit |
| UC-16 | Tugaskan teknisi | Kepala IPSRS |
| UC-17 | Lihat jadwal perawatan | Teknisi |
| UC-18 | Catat hasil perawatan | Teknisi |
| UC-19 | Kerjakan dan tutup work order | Teknisi |
| UC-20 | Catat kalibrasi dan unggah sertifikat | Teknisi biomedis |
| UC-21 | Lihat dashboard | Manajemen, kepala unit, admin |
| UC-22 | Buat kode aset dan QR | Sistem |
| UC-23 | Buat jadwal perawatan berikutnya | Sistem |
| UC-24 | Kirim pengingat perawatan (in-app + email) | Sistem (Scheduler) |
| UC-25 | Catat audit log | Sistem |
| UC-26 | Kirim pengingat kalibrasi (in-app + email) | Sistem (Scheduler) |

---

## 9. Alur Sistem (Flowchart Teks)

[Detail Kasus 1 s/d Kasus 8 tercantum lengkap di PRD & Panduan Agent]

---

## 10. Notifikasi (Standar Umum) **[Usulan]**

| Jenis | Kanal | Penyimpanan | Contoh |
| --- | --- | --- | --- |
| Validasi form | Inline di bawah field | Tidak disimpan | "Kategori wajib dipilih" |
| Peringatan aturan bisnis | Alert di atas form | Tidak disimpan | Lokasi tujuan sama |
| Hasil aksi | Toast (pojok kanan atas) | Tidak disimpan | "Aset berhasil ditambahkan" |
| Tugas dan pengingat | In-app + email | Tabel `notifications` | Tugas perawatan, pengingat H-30 |
| Eskalasi | In-app + email | Tabel `notifications` | Perawatan terlambat > 7 hari |

---

## 11. Desain Database

Database: **MySQL 8**. Urutan migrasi:
1. `roles`, `vendors`, `categories`
2. `departments` (tanpa `head_user_id`)
3. `users`
4. tambah FK `departments.head_user_id`
5. `locations`
6. `assets`
7. `asset_documents`, `asset_movements`, `maintenance_plans`, `calibrations`, `work_orders`
8. `maintenance_logs`, `work_order_updates`, `calibration_reminders`
9. `stock_opname_sessions`, `stock_opname_items`
10. `notifications`, `audit_logs`

---

## 12. Aturan Bisnis

1. Setiap perubahan `location_id` aset wajib lewat `asset_movements` dalam satu transaksi. **[PRD]**
2. Log perawatan selesai → `next_due_date = performed_at + interval_days`. **[PRD]**
3. Tiket status `dikerjakan` → aset `dalam_perbaikan`. Tiket `ditutup` → aset kembali `aktif` sesuai `final_condition`. **[PRD]**
4. Kondisi akhir `rusak_berat` → aset `tidak_aktif` lalu dinilai untuk penghapusan. **[Usulan]**
5. Kalibrasi hanya wajib untuk kategori dengan `requires_calibration = true`. **[PRD]**
6. Scheduler harian menandai `terlambat` dan mengirim pengingat in-app + email. **[PRD + Usulan]**
7. Terlambat > 7 hari → eskalasi ke Kepala Unit. **[PRD, bagian Risiko]**
8. Pengingat kalibrasi: H-60, H-30, H-7, H-3, H-1, dan kedaluwarsa. Setiap ambang terkirim tepat sekali. **[PRD + Usulan]**
9. Kalibrasi gagal → aset tidak boleh dipakai klinis dan dibuat tiket Kritis. **[Usulan]**

---

## 13. Fase Pengerjaan

- **Fase 0. Persiapan:** Validasi PRD, kumpulkan data aset, finalisasi peran, konfirmasi 12 keputusan di Bagian 15.
- **Fase 1. Fondasi:** Auth (Bootstrap), RBAC, data master, layout CoreUI.
- **Fase 2. Inti aset:** Registrasi, QR, pencarian, impor, mutasi.
- **Fase 3. Perawatan dan perbaikan:** Preventif, kalibrasi, pengingat in-app + email, tiket.
- **Fase 4. Opname dan laporan:** Stock opname, dashboard, ekspor.
- **Fase 5. UAT dan pilot:** Pilot di 2–3 unit (ICU, Radiologi, IPSRS).

---

## 14. Design System (Bootstrap 5 + CoreUI)

- Kelas Bootstrap 5 & CoreUI: `btn btn-primary`, `alert alert-warning`, `badge text-bg-success`.
- Warna custom ditaruh di `public/css/simas.css`, dimuat setelah CoreUI.

---

## 15. Keputusan yang Harus Disetujui Sebelum Coding

1. Database: MySQL 8 atau PostgreSQL (PRD) → **Usulan: MySQL 8**
2. Mutasi perlu persetujuan kepala unit? → **Usulan: Opsional, dinyalakan per unit**
3. Status aset saat tiket dikerjakan → **Usulan: `dalam_perbaikan`**
4. Status aset saat kondisi akhir `rusak_berat` → **Usulan: `tidak_aktif`**
5. Kalibrasi gagal: aset boleh dipakai? → **Usulan: Tidak boleh, buat tiket Kritis**
6. `assets.vendor_id` boleh kosong? → **Usulan: Boleh kosong**
7. Teknisi boleh melapor kerusakan? → **Usulan: Boleh**
8. Penerima email pengingat kalibrasi → **Usulan: Sesuai tabel di Kasus 7**
9. Email gagal dikirim: retry berapa kali → **Usulan: 3 kali, lalu laporan harian ke admin**
10. Jam kirim pengingat harian → **Usulan: 07:00 WIB**
11. Library scan QR dan ekspor PDF/Excel → **Usulan: Disetujui sesuai bagian 2**
12. Server on-premise atau cloud → **Usulan: On-premise (Docker di server/VPS RS)**

