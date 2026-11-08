<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\Vendor;
use App\Support\AssetCodeGenerator;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetExcelService
{
    /**
     * Unduh berkas template Excel .xlsx untuk impor inventaris aset.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Aset');

        // Headers
        $headers = [
            'A1' => 'Kode Aset (Opsional)',
            'B1' => 'Nama Aset (Wajib)',
            'C1' => 'Stok (Wajib Angka)',
            'D1' => 'Safety Stock (Wajib Angka)',
            'E1' => 'Merek',
            'F1' => 'Model / Tipe',
            'G1' => 'Nomor Seri',
            'H1' => 'Tanggal Perolehan (YYYY-MM-DD)',
            'I1' => 'Biaya Perolehan (Angka Rp)',
            'J1' => 'Sumber Dana',
            'K1' => 'Batas Garansi (YYYY-MM-DD)',
            'L1' => 'Kondisi (Baik / Rusak)',
            'M1' => 'Status (Aktif / Dalam Perbaikan / Non Aktif / Hilang)',
            'N1' => 'Aset Kritis (1 = Ya, 0 = Tidak)',
            'O1' => 'Kode Kategori (Wajib)',
            'P1' => 'Kode Ruangan (Wajib)',
            'Q1' => 'Kode Unit / Departemen (Wajib)',
            'R1' => 'Nama Vendor Rekanan',
            'S1' => 'Catatan',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Contoh Data Baris
        $examples = [
            [
                '',
                'Syringe Pump Otomatis Micro-Dose',
                '5',
                '2',
                'Terumo',
                'TE-SS700',
                'TRM-SYR-9901',
                '2024-06-01',
                '28000000',
                'Operasional RS',
                '2026-06-01',
                'Baik',
                'Aktif',
                '1',
                'MED-LIFE',
                'R-ICU-01',
                'ICU',
                'PT Medika Alkes Indonesia',
                'Pompa infus presisi tinggi untuk rawat intensif',
            ],
            [
                '',
                'Mobile Patient Monitor 5-Parameter',
                '3',
                '1',
                'Mindray',
                'ePM 10M',
                'MN-EPM-7721',
                '2024-07-15',
                '65000000',
                'BLUD RS UMMI',
                '2026-07-15',
                'Baik',
                'Aktif',
                '1',
                'MED-LIFE',
                'R-IGD-RESUS',
                'IGD',
                'PT Medika Alkes Indonesia',
                'Monitor transport pasien darurat',
            ],
        ];

        $row = 2;
        foreach ($examples as $data) {
            $colLetter = 'A';
            foreach ($data as $val) {
                $sheet->setCellValueExplicit("{$colLetter}{$row}", (string) $val, DataType::TYPE_STRING);
                $colLetter++;
            }
            $row++;
        }

        // Format Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:S1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Auto width
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Catatan Petunjuk Pengisian
        $noteRow = $row + 2;
        $sheet->setCellValue("A{$noteRow}", 'PETUNJUK PENGISIAN TEMPLATE IMPORT INVENTARIS ASET:');
        $sheet->getStyle("A{$noteRow}")->getFont()->setBold(true);
        $sheet->setCellValue('A'.($noteRow + 1), '1. Berkas wajib berformat Microsoft Excel .xlsx.');
        $sheet->setCellValue('A'.($noteRow + 2), '2. Kode Aset boleh dikosongkan; sistem akan membuat kode otomatis (AST-YYYY-XXXXXX).');
        $sheet->setCellValue('A'.($noteRow + 3), '3. Stok dan Safety Stock wajib diisi angka bulat (minimal 0).');
        $sheet->setCellValue('A'.($noteRow + 4), '4. Kondisi: diisi "Baik" atau "Rusak".');
        $sheet->setCellValue('A'.($noteRow + 5), '5. Status: diisi "Aktif", "Dalam Perbaikan", "Non Aktif", atau "Hilang".');
        $sheet->setCellValue('A'.($noteRow + 6), '6. Kode Kategori, Ruangan, dan Unit harus sesuai master data (cth: MED-LIFE, R-ICU-01, ICU).');

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'Template_Import_Aset_SIMAS_RS.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Ekspor data inventaris aset ke format Excel .xlsx.
     */
    public function export(Collection $assets): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaris Aset');

        // Judul Dokumen
        $sheet->setCellValue('A1', 'RUMAH SAKIT UMMI BOGOR - LAPORAN INVENTARIS ASET');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'Waktu Unduh: '.now()->translatedFormat('d F Y H:i:s').' | Total: '.$assets->count().' Aset Terdaftar');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

        // Header Tabel
        $headers = [
            'A4' => 'No',
            'B4' => 'Kode Aset',
            'C4' => 'Nama Aset',
            'D4' => 'Stok',
            'E4' => 'Safety Stock',
            'F4' => 'Merek',
            'G4' => 'Model / Tipe',
            'H4' => 'Nomor Seri',
            'I4' => 'Tanggal Perolehan',
            'J4' => 'Biaya Perolehan (Rp)',
            'K4' => 'Sumber Dana',
            'L4' => 'Batas Garansi',
            'M4' => 'Kondisi',
            'N4' => 'Status',
            'O4' => 'Aset Kritis',
            'P4' => 'Kategori',
            'Q4' => 'Ruangan / Lokasi',
            'R4' => 'Unit / Departemen',
            'S4' => 'Vendor Rekanan',
            'T4' => 'Catatan',
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F766E'], // Teal Dark
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A4:T4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(25);

        $row = 5;
        foreach ($assets as $index => $asset) {
            $conditionLabel = $asset->condition instanceof AssetCondition ? $asset->condition->getLabel() : ucfirst((string) $asset->condition);
            $statusLabel = $asset->status instanceof AssetStatus ? $asset->status->getLabel() : ucfirst((string) $asset->status);

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValueExplicit("B{$row}", $asset->asset_code ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $asset->name ?? '-');
            $sheet->setCellValue("D{$row}", (int) $asset->stock);
            $sheet->setCellValue("E{$row}", (int) $asset->safety_stock);
            $sheet->setCellValue("F{$row}", $asset->brand ?? '-');
            $sheet->setCellValue("G{$row}", $asset->model ?? '-');
            $sheet->setCellValueExplicit("H{$row}", $asset->serial_number ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("I{$row}", $asset->acquisition_date ? $asset->acquisition_date->format('d/m/Y') : '-');
            $sheet->setCellValue("J{$row}", (float) $asset->acquisition_cost);
            $sheet->setCellValue("K{$row}", $asset->funding_source ?? '-');
            $sheet->setCellValue("L{$row}", $asset->warranty_expiry_date ? $asset->warranty_expiry_date->format('d/m/Y') : '-');
            $sheet->setCellValue("M{$row}", $conditionLabel);
            $sheet->setCellValue("N{$row}", $statusLabel);
            $sheet->setCellValue("O{$row}", $asset->is_critical ? 'Ya' : 'Tidak');
            $sheet->setCellValue("P{$row}", $asset->category->name ?? '-');
            $sheet->setCellValue("Q{$row}", $asset->location->name ?? '-');
            $sheet->setCellValue("R{$row}", $asset->department->name ?? '-');
            $sheet->setCellValue("S{$row}", $asset->vendor->name ?? '-');
            $sheet->setCellValue("T{$row}", $asset->notes ?? '-');

            $row++;
        }

        $dataRange = 'A5:T'.max(5, $row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A5:A'.max(5, $row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B5:B'.max(5, $row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5:E'.max(5, $row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M5:O'.max(5, $row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Format angka biaya
        $sheet->getStyle('J5:J'.max(5, $row - 1))->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Laporan_Inventaris_Aset_SIMAS_RS_'.now()->format('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Impor data aset dari file Excel .xlsx.
     *
     * @return array{success: int, failed: int, errors: list<string>}
     */
    public function import(string $filePath, int $userId): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($extension !== 'xlsx') {
            throw new \InvalidArgumentException('Format file tidak valid. Sistem hanya menerima file Microsoft Excel berekstensi .xlsx');
        }

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        // Lewati baris 0 (header)
        for ($i = 1; $i < count($rows); $i++) {
            $rowNumber = $i + 1;
            $row = $rows[$i];

            $assetCode = trim((string) ($row[0] ?? ''));
            $name = trim((string) ($row[1] ?? ''));
            $stock = (int) ($row[2] ?? 1);
            $safetyStock = (int) ($row[3] ?? 1);
            $brand = trim((string) ($row[4] ?? ''));
            $model = trim((string) ($row[5] ?? ''));
            $serialNumber = trim((string) ($row[6] ?? ''));
            $acquisitionDate = trim((string) ($row[7] ?? ''));
            $acquisitionCost = (float) ($row[8] ?? 0);
            $fundingSource = trim((string) ($row[9] ?? ''));
            $warrantyExpiryDate = trim((string) ($row[10] ?? ''));
            $conditionStr = strtolower(trim((string) ($row[11] ?? 'baik')));
            $statusStr = strtolower(trim((string) ($row[12] ?? 'aktif')));
            $isCriticalVal = trim((string) ($row[13] ?? '0'));
            $categoryIdentifier = trim((string) ($row[14] ?? ''));
            $locationIdentifier = trim((string) ($row[15] ?? ''));
            $departmentIdentifier = trim((string) ($row[16] ?? ''));
            $vendorName = trim((string) ($row[17] ?? ''));
            $notes = trim((string) ($row[18] ?? ''));

            // Abaikan baris kosong atau baris catatan petunjuk
            if ($name === '' || str_starts_with(strtoupper($name), 'PETUNJUK')) {
                continue;
            }

            // 1. Temukan Kategori
            $category = Category::where('code', $categoryIdentifier)
                ->orWhere('name', $categoryIdentifier)
                ->first();

            if (! $category) {
                $category = Category::first();
            }

            // 2. Temukan Ruangan
            $location = Location::where('code', $locationIdentifier)
                ->orWhere('name', $locationIdentifier)
                ->first();

            if (! $location) {
                $location = Location::first();
            }

            // 3. Temukan Departemen
            $department = Department::where('code', $departmentIdentifier)
                ->orWhere('name', $departmentIdentifier)
                ->first();

            if (! $department) {
                $department = $location?->department ?? Department::first();
            }

            // 4. Temukan / Buat Vendor jika diisi
            $vendorId = null;
            if ($vendorName !== '') {
                $vendor = Vendor::firstOrCreate(
                    ['name' => $vendorName],
                    ['type' => 'supplier']
                );
                $vendorId = $vendor->id;
            }

            // 5. Parse Status & Kondisi
            $condition = match ($conditionStr) {
                'rusak' => AssetCondition::RUSAK,
                default => AssetCondition::BAIK,
            };

            $status = match ($statusStr) {
                'dalam perbaikan', 'dalam_perbaikan' => AssetStatus::DALAM_PERBAIKAN,
                'non aktif', 'non_aktif' => AssetStatus::NON_AKTIF,
                'hilang' => AssetStatus::HILANG,
                default => AssetStatus::AKTIF,
            };

            $isCritical = in_array(strtolower($isCriticalVal), ['1', 'ya', 'true', 'yes'], true);

            // 6. Buat Kode Aset jika belum ada
            if ($assetCode === '') {
                $assetCode = AssetCodeGenerator::generate();
            }

            try {
                Asset::updateOrCreate(
                    ['asset_code' => $assetCode],
                    [
                        'name' => $name,
                        'stock' => max(0, $stock),
                        'safety_stock' => max(0, $safetyStock),
                        'brand' => $brand ?: null,
                        'model' => $model ?: null,
                        'serial_number' => $serialNumber ?: null,
                        'acquisition_date' => $acquisitionDate ? date('Y-m-d', strtotime($acquisitionDate)) : null,
                        'acquisition_cost' => max(0, $acquisitionCost),
                        'funding_source' => $fundingSource ?: null,
                        'warranty_expiry_date' => $warrantyExpiryDate ? date('Y-m-d', strtotime($warrantyExpiryDate)) : null,
                        'condition' => $condition,
                        'status' => $status,
                        'is_critical' => $isCritical,
                        'category_id' => $category?->id,
                        'location_id' => $location?->id,
                        'department_id' => $department?->id,
                        'vendor_id' => $vendorId,
                        'notes' => $notes ?: null,
                        'created_by' => $userId,
                    ]
                );

                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: {$e->getMessage()}";
            }
        }

        return [
            'success' => $successCount,
            'failed' => $failedCount,
            'errors' => $errors,
        ];
    }
}
