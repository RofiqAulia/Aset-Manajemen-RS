<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Enums\AssetMovementStatus;
use App\Enums\AssetStatus;
use App\Enums\CalibrationResult;
use App\Enums\MaintenanceLogStatus;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Calibration;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\MaintenanceLog;
use App\Models\User;
use App\Models\Vendor;
use App\Support\AssetCodeGenerator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SampleAssetDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $icuDept = Department::where('code', 'ICU')->first();
        $igdDept = Department::where('code', 'IGD')->first();
        $radDept = Department::where('code', 'RAD')->first();
        $ipsrsDept = Department::where('code', 'IPSRS')->first();

        // 1. Kategori Master
        $catLife = Category::firstOrCreate(
            ['code' => 'MED-LIFE'],
            [
                'name' => 'Alat Penunjang Hidup (Life Support)',
                'is_medical' => true,
                'requires_calibration' => true,
                'default_maintenance_interval_days' => 30,
                'default_calibration_interval_days' => 180,
            ]
        );

        $catDiag = Category::firstOrCreate(
            ['code' => 'MED-DIAG'],
            [
                'name' => 'Alat Diagnostik Medis',
                'is_medical' => true,
                'requires_calibration' => true,
                'default_maintenance_interval_days' => 90,
                'default_calibration_interval_days' => 365,
            ]
        );

        $catIT = Category::firstOrCreate(
            ['code' => 'IT-HW'],
            [
                'name' => 'Perangkat Keras & Jaringan IT',
                'is_medical' => false,
                'requires_calibration' => false,
                'default_maintenance_interval_days' => 180,
            ]
        );

        // 2. Vendor Master
        $vendor1 = Vendor::firstOrCreate(
            ['name' => 'PT Medika Alkes Indonesia'],
            [
                'contact_person' => 'Budi Santoso',
                'phone' => '021-5551234',
                'email' => 'contact@medikaalkes.co.id',
                'address' => 'Jl. Kesehatan No. 45, Jakarta',
                'type' => 'supplier',
            ]
        );

        $vendor2 = Vendor::firstOrCreate(
            ['name' => 'PT Kalibrasi Presisi Medika'],
            [
                'contact_person' => 'Ir. Hendra',
                'phone' => '022-7778899',
                'email' => 'info@presisimedika.com',
                'address' => 'Kawasan Industri Bandung Tekno',
                'type' => 'kalibrasi',
            ]
        );

        // 3. Lokasi / Ruangan Master
        $gedungUtama = Location::firstOrCreate(
            ['code' => 'G-UTAMA'],
            [
                'name' => 'Gedung Utama RS UMMI',
                'type' => 'building',
            ]
        );

        $lantai1 = Location::firstOrCreate(
            ['code' => 'LT-1'],
            [
                'parent_id' => $gedungUtama->id,
                'name' => 'Lantai 1 - Pelayanan Gawat Darurat & Radiologi',
                'type' => 'floor',
            ]
        );

        $lantai2 = Location::firstOrCreate(
            ['code' => 'LT-2'],
            [
                'parent_id' => $gedungUtama->id,
                'name' => 'Lantai 2 - Rawat Intensif & Bedah',
                'type' => 'floor',
            ]
        );

        $ruangICU = Location::firstOrCreate(
            ['code' => 'R-ICU-01'],
            [
                'parent_id' => $lantai2->id,
                'name' => 'Ruang Perawatan Intensif (ICU Bed 1-6)',
                'type' => 'room',
                'department_id' => $icuDept?->id,
            ]
        );

        $ruangIGD = Location::firstOrCreate(
            ['code' => 'R-IGD-RESUS'],
            [
                'parent_id' => $lantai1->id,
                'name' => 'Ruang Resusitasi Gawat Darurat',
                'type' => 'room',
                'department_id' => $igdDept?->id,
            ]
        );

        $ruangXRay = Location::firstOrCreate(
            ['code' => 'R-RAD-XRAY'],
            [
                'parent_id' => $lantai1->id,
                'name' => 'Ruang Pemeriksaan X-Ray Digital',
                'type' => 'room',
                'department_id' => $radDept?->id,
            ]
        );

        // 4. Sample Data Aset
        $sampleAssets = [
            [
                'name' => 'Ventilator Mekanik ICU Hamilton-C6',
                'brand' => 'Hamilton Medical',
                'model' => 'Hamilton-C6',
                'serial_number' => 'HAM-2024-8831',
                'stock' => 4,
                'safety_stock' => 2,
                'acquisition_date' => '2024-03-15',
                'acquisition_cost' => 450000000.00,
                'funding_source' => 'BLUD RS UMMI',
                'warranty_expiry_date' => '2027-03-15',
                'condition' => AssetCondition::BAIK,
                'status' => AssetStatus::AKTIF,
                'is_critical' => true,
                'notes' => 'Alat life support kritis, terpasang di Bed 1 ICU.',
                'category_id' => $catLife->id,
                'location_id' => $ruangICU->id,
                'department_id' => $icuDept?->id ?? 1,
                'vendor_id' => $vendor1->id,
            ],
            [
                'name' => 'Bedside Patient Monitor 7-Parameter',
                'brand' => 'Mindray',
                'model' => 'BeneVision N12',
                'serial_number' => 'MN-BVN12-0942',
                'stock' => 6,
                'safety_stock' => 2,
                'acquisition_date' => '2024-05-10',
                'acquisition_cost' => 85000000.00,
                'funding_source' => 'Operasional RS',
                'warranty_expiry_date' => '2026-05-10',
                'condition' => AssetCondition::BAIK,
                'status' => AssetStatus::AKTIF,
                'is_critical' => true,
                'notes' => 'Monitor tanda vital di Ruang Tindakan IGD.',
                'category_id' => $catLife->id,
                'location_id' => $ruangIGD->id,
                'department_id' => $igdDept?->id ?? 1,
                'vendor_id' => $vendor1->id,
            ],
            [
                'name' => 'Stationary Digital X-Ray System',
                'brand' => 'Shimadzu',
                'model' => 'RADspeed Pro',
                'serial_number' => 'SHM-XRAY-7741',
                'stock' => 2,
                'safety_stock' => 1,
                'acquisition_date' => '2023-11-20',
                'acquisition_cost' => 1250000000.00,
                'funding_source' => 'DANA DAK Kemenkes',
                'warranty_expiry_date' => '2026-11-20',
                'condition' => AssetCondition::BAIK,
                'status' => AssetStatus::AKTIF,
                'is_critical' => false,
                'notes' => 'Pesawat rontgen digital utama radiologi.',
                'category_id' => $catDiag->id,
                'location_id' => $ruangXRay->id,
                'department_id' => $radDept?->id ?? 1,
                'vendor_id' => $vendor2->id,
            ],
            [
                'name' => 'Defibrillator Biphasic Plus AED',
                'brand' => 'Zoll Medical',
                'model' => 'R Series Plus',
                'serial_number' => 'ZOL-DEF-3321',
                'stock' => 3,
                'safety_stock' => 2,
                'acquisition_date' => '2024-01-08',
                'acquisition_cost' => 175000000.00,
                'funding_source' => 'BLUD RS UMMI',
                'warranty_expiry_date' => '2027-01-08',
                'condition' => AssetCondition::BAIK,
                'status' => AssetStatus::AKTIF,
                'is_critical' => true,
                'notes' => 'Alat kejut jantung emergency trolley IGD.',
                'category_id' => $catLife->id,
                'location_id' => $ruangIGD->id,
                'department_id' => $igdDept?->id ?? 1,
                'vendor_id' => $vendor1->id,
            ],
            [
                'name' => 'Syringe Pump Otomatis Micro-Dose',
                'brand' => 'Terumo',
                'model' => 'TE-SS700',
                'serial_number' => 'TRM-SYR-5542',
                'stock' => 1,
                'safety_stock' => 3,
                'acquisition_date' => '2024-06-01',
                'acquisition_cost' => 28000000.00,
                'funding_source' => 'Operasional RS',
                'warranty_expiry_date' => '2026-06-01',
                'condition' => AssetCondition::BAIK,
                'status' => AssetStatus::AKTIF,
                'is_critical' => true,
                'notes' => 'Stok menipis di bawah safety stock instalasi farmasi/ICU.',
                'category_id' => $catLife->id,
                'location_id' => $ruangICU->id,
                'department_id' => $icuDept?->id ?? 1,
                'vendor_id' => $vendor1->id,
            ],
            [
                'name' => 'Suction Pump Portable Emergency',
                'brand' => 'Atmos',
                'model' => 'C 341 Battery',
                'serial_number' => 'ATM-SUC-9912',
                'stock' => 0,
                'safety_stock' => 2,
                'acquisition_date' => '2023-09-10',
                'acquisition_cost' => 17000000.00,
                'funding_source' => 'BLUD RS UMMI',
                'warranty_expiry_date' => '2025-09-10',
                'condition' => AssetCondition::RUSAK,
                'status' => AssetStatus::DALAM_PERBAIKAN,
                'is_critical' => false,
                'notes' => 'Unit rusak dalam proses perbaikan, stok saat ini habis.',
                'category_id' => $catLife->id,
                'location_id' => $ruangIGD->id,
                'department_id' => $igdDept?->id ?? 1,
                'vendor_id' => $vendor1->id,
            ],
        ];

        $createdAssets = [];
        foreach ($sampleAssets as $assetData) {
            $existing = Asset::where('serial_number', $assetData['serial_number'])->first();
            if (! $existing) {
                $assetData['asset_code'] = AssetCodeGenerator::generate();
                $assetData['created_by'] = $admin?->id ?? 1;
                $createdAssets[] = Asset::create($assetData);
            } else {
                $existing->update([
                    'stock' => $assetData['stock'],
                    'safety_stock' => $assetData['safety_stock'],
                    'condition' => $assetData['condition'],
                    'status' => $assetData['status'],
                ]);
                $createdAssets[] = $existing;
            }
        }

        // 5. Sample Data Kalibrasi
        $ventilator = Asset::where('serial_number', 'HAM-2024-8831')->first();
        $defibrillator = Asset::where('serial_number', 'ZOL-DEF-3321')->first();
        $monitor = Asset::where('serial_number', 'MN-BVN12-0942')->first();

        if ($ventilator) {
            Calibration::firstOrCreate(
                ['certificate_number' => 'KAL-HAM-2026-01'],
                [
                    'asset_id' => $ventilator->id,
                    'vendor_id' => $vendor2->id,
                    'calibration_date' => Carbon::now()->subMonths(5),
                    'expiry_date' => Carbon::now()->addDays(14), // Jatuh tempo dalam 14 hari
                    'result' => CalibrationResult::LULUS,
                    'cost' => 3500000.00,
                    'tested_by' => 'Balai Pengujian Fasilitas Kesehatan (BPFK)',
                    'notes' => 'Akurasi sensor flow dan pressure dalam toleransi standar.',
                ]
            );
        }

        if ($defibrillator) {
            Calibration::firstOrCreate(
                ['certificate_number' => 'KAL-ZOL-2026-02'],
                [
                    'asset_id' => $defibrillator->id,
                    'vendor_id' => $vendor2->id,
                    'calibration_date' => Carbon::now()->subMonths(11),
                    'expiry_date' => Carbon::now()->addDays(22), // Jatuh tempo dalam 22 hari
                    'result' => CalibrationResult::LULUS,
                    'cost' => 2800000.00,
                    'tested_by' => 'PT Kalibrasi Presisi Medika',
                    'notes' => 'Keluaran energi joule dan waktu discharge sinkron terverifikasi.',
                ]
            );
        }

        if ($monitor) {
            Calibration::firstOrCreate(
                ['certificate_number' => 'KAL-MN-2026-03'],
                [
                    'asset_id' => $monitor->id,
                    'vendor_id' => $vendor2->id,
                    'calibration_date' => Carbon::now()->subMonths(2),
                    'expiry_date' => Carbon::now()->addMonths(10), // Masih aman
                    'result' => CalibrationResult::LULUS,
                    'cost' => 1500000.00,
                    'tested_by' => 'PT Kalibrasi Presisi Medika',
                    'notes' => 'SpO2, NIBP, dan lead ECG teruji normal.',
                ]
            );
        }

        // 6. Sample Log Aktivitas Harian (MaintenanceLog)
        if ($admin && ! empty($createdAssets)) {
            $logs = [
                [
                    'asset_id' => $ventilator?->id ?? $createdAssets[0]->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::SELESAI,
                    'cost' => 0,
                    'findings' => 'Pemeriksaan rutin harian fungsi O2 cell, breathing circuit, dan pressure test.',
                    'action_taken' => 'Kalibrasi mandiri (pre-use check) berhasil normal, unit siap pakai.',
                ],
                [
                    'asset_id' => $defibrillator?->id ?? $createdAssets[1]->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::SELESAI,
                    'cost' => 0,
                    'findings' => 'Baterai internal 100%, paddle test discharge pada 30 Joule normal.',
                    'action_taken' => 'Penggantian gel defibrilasi cadangan dan pembersihan kontak baterai.',
                ],
                [
                    'asset_id' => $monitor?->id ?? $createdAssets[2]->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDay()->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::SELESAI,
                    'cost' => 250000.00,
                    'findings' => 'Kabel sensor SpO2 mengalami kontak intermittent pada socket modul.',
                    'action_taken' => 'Pembersihan pin konektor dan penggantian reusable adult sensor SpO2 baru.',
                ],
                [
                    'asset_id' => $createdAssets[3]->id ?? $ventilator?->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDays(2)->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::DIJADWALKAN,
                    'cost' => 0,
                    'findings' => 'Inspeksi preventif berkala kelistrikan dan grounding ruang tindakan.',
                    'action_taken' => 'Penjadwalan uji kebocoran arus (electrical safety test) bersama IPSRS.',
                ],
                [
                    'asset_id' => $createdAssets[4]->id ?? $ventilator?->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDays(4)->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::SELESAI,
                    'cost' => 150000.00,
                    'findings' => 'Verifikasi akurasi infus rate dan sensor deteksi oklusi selang.',
                    'action_taken' => 'Uji kalibrasi debit tetesan (drop rate test) dan pembersihan housing syringe.',
                ],
                [
                    'asset_id' => $createdAssets[0]->id ?? $ventilator?->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDays(5)->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::SELESAI,
                    'cost' => 0,
                    'findings' => 'Pembersihan filter udara HEPA dan sterilisasi sirkuit pernapasan.',
                    'action_taken' => 'Penggantian filter bakteri expiratory cassette.',
                ],
                [
                    'asset_id' => $createdAssets[1]->id ?? $ventilator?->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDays(6)->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::SELESAI,
                    'cost' => 0,
                    'findings' => 'Pengujian alarm batas atas dan bawah parameter hemodinamik.',
                    'action_taken' => 'Simulasi sinyal ECG dan NIBP menggunakan patient simulator.',
                ],
                [
                    'asset_id' => $createdAssets[2]->id ?? $ventilator?->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDays(7)->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::SELESAI,
                    'cost' => 500000.00,
                    'findings' => 'Inspeksi berkala tabung sinar-X dan pelindung radiasi ruang radiologi.',
                    'action_taken' => 'Pengukuran radiasi bocor dan uji kolimator berkas cahaya.',
                ],
                [
                    'asset_id' => $createdAssets[3]->id ?? $ventilator?->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDays(8)->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::SELESAI,
                    'cost' => 0,
                    'findings' => 'Pengecekan kelengkapan electrode pads dewasa dan anak.',
                    'action_taken' => 'Inventarisasi ulang aksesori darurat trolley resusitasi IGD.',
                ],
                [
                    'asset_id' => $createdAssets[4]->id ?? $ventilator?->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDays(9)->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::TERLAMBAT,
                    'cost' => 0,
                    'findings' => 'Inspeksi berkala baterai internal unit tertunda karena ruang perawatan penuh.',
                    'action_taken' => 'Penjadwalan ulang pemeliharaan preventif pada shift berikutnya.',
                ],
                [
                    'asset_id' => $createdAssets[5]->id ?? $ventilator?->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDays(10)->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::DIJADWALKAN,
                    'cost' => 350000.00,
                    'findings' => 'Penggantian motor pompa vakum dan segel tabung penampung.',
                    'action_taken' => 'Pengadaan suku cadang seal silicone dan uji daya hisap negatif.',
                ],
                [
                    'asset_id' => $createdAssets[0]->id ?? $ventilator?->id,
                    'technician_id' => $admin->id,
                    'performed_at' => Carbon::now()->subDays(12)->format('Y-m-d'),
                    'status' => MaintenanceLogStatus::SELESAI,
                    'cost' => 0,
                    'findings' => 'Inspeksi visual supply gas medis (O2 dan Air) tekanan 4 bar.',
                    'action_taken' => 'Pembersihan regulator inlet gas dan pemeriksaan selang penghubung.',
                ],
            ];

            foreach ($logs as $logData) {
                MaintenanceLog::firstOrCreate(
                    [
                        'asset_id' => $logData['asset_id'],
                        'performed_at' => $logData['performed_at'],
                        'findings' => $logData['findings'],
                    ],
                    $logData
                );
            }

            // 7. Sample Data Mutasi Lokasi (AssetMovement)
            if (count($createdAssets) >= 3 && $ruangIGD && $ruangICU && $ruangXRay) {
                AssetMovement::firstOrCreate(
                    [
                        'asset_id' => $monitor?->id ?? $createdAssets[1]->id,
                        'reason' => 'Kebutuhan darurat penambahan monitor pasien di ruang ICU.',
                    ],
                    [
                        'asset_id' => $monitor?->id ?? $createdAssets[1]->id,
                        'from_location_id' => $ruangIGD->id,
                        'to_location_id' => $ruangICU->id,
                        'from_department_id' => $igdDept?->id ?? 1,
                        'to_department_id' => $icuDept?->id ?? 1,
                        'moved_by' => $admin->id,
                        'approved_by' => null,
                        'status' => AssetMovementStatus::MENUNGGU_PERSETUJUAN,
                        'moved_at' => Carbon::now()->subHours(3),
                        'reason' => 'Kebutuhan darurat penambahan monitor pasien di ruang ICU.',
                    ]
                );

                AssetMovement::firstOrCreate(
                    [
                        'asset_id' => $defibrillator?->id ?? $createdAssets[3]->id,
                        'reason' => 'Relokasi berkala alat defibrillator dari IGD ke ICU pasca sterilisasi.',
                    ],
                    [
                        'asset_id' => $defibrillator?->id ?? $createdAssets[3]->id,
                        'from_location_id' => $ruangIGD->id,
                        'to_location_id' => $ruangICU->id,
                        'from_department_id' => $igdDept?->id ?? 1,
                        'to_department_id' => $icuDept?->id ?? 1,
                        'moved_by' => $admin->id,
                        'approved_by' => $admin->id,
                        'status' => AssetMovementStatus::DISETUJUI,
                        'moved_at' => Carbon::now()->subDays(2),
                        'reason' => 'Relokasi berkala alat defibrillator dari IGD ke ICU pasca sterilisasi.',
                    ]
                );

                AssetMovement::firstOrCreate(
                    [
                        'asset_id' => $createdAssets[4]->id,
                        'reason' => 'Permintaan pemindahan syringe pump ke poliklinik rawat jalan.',
                    ],
                    [
                        'asset_id' => $createdAssets[4]->id,
                        'from_location_id' => $ruangICU->id,
                        'to_location_id' => $ruangXRay->id,
                        'from_department_id' => $icuDept?->id ?? 1,
                        'to_department_id' => $radDept?->id ?? 1,
                        'moved_by' => $admin->id,
                        'approved_by' => $admin->id,
                        'status' => AssetMovementStatus::DITOLAK,
                        'moved_at' => Carbon::now()->subDays(5),
                        'reason' => 'Permintaan pemindahan syringe pump ke poliklinik rawat jalan.',
                        'rejection_reason' => 'Stok syringe pump di ICU sedang menipis dan diprioritaskan untuk pasien kritis.',
                    ]
                );
            }
        }
    }
}
