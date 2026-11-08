<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Models\User;
use App\Services\AssetExcelService;
use Database\Seeders\InitialMasterSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleAssetDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class AssetExcelTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_template_download_generates_valid_xlsx(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        Livewire::actingAs($user)
            ->test(ListAssets::class)
            ->callTableAction('download_template')
            ->assertFileDownloaded('Template_Import_Aset_SIMAS_RS.xlsx');
    }

    public function test_asset_export_excel_downloads_xlsx_file(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        Livewire::actingAs($user)
            ->test(ListAssets::class)
            ->callTableAction('export_excel')
            ->assertFileDownloaded();
    }

    public function test_asset_import_service_rejects_non_xlsx_file(): void
    {
        $service = app(AssetExcelService::class);

        $this->expectException(\InvalidArgumentException::class);
        $service->import('test-file.csv', 1);
    }

    public function test_asset_import_service_processes_valid_xlsx(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        // Create temporary spreadsheet for asset import
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Kode Aset');
        $sheet->setCellValue('B1', 'Nama Aset');
        $sheet->setCellValue('C1', 'Stok');
        $sheet->setCellValue('D1', 'Safety Stock');
        $sheet->setCellValue('E1', 'Merek');
        $sheet->setCellValue('F1', 'Model');
        $sheet->setCellValue('G1', 'Serial');
        $sheet->setCellValue('H1', 'Tgl Perolehan');
        $sheet->setCellValue('I1', 'Biaya');
        $sheet->setCellValue('J1', 'Sumber');
        $sheet->setCellValue('K1', 'Garansi');
        $sheet->setCellValue('L1', 'Kondisi');
        $sheet->setCellValue('M1', 'Status');
        $sheet->setCellValue('N1', 'Kritis');
        $sheet->setCellValue('O1', 'Kategori');
        $sheet->setCellValue('P1', 'Ruangan');
        $sheet->setCellValue('Q1', 'Unit');
        $sheet->setCellValue('R1', 'Vendor');
        $sheet->setCellValue('S1', 'Catatan');

        $sheet->setCellValue('A2', 'AST-TEST-999');
        $sheet->setCellValue('B2', 'Pulse Oximeter Fingertip Medis');
        $sheet->setCellValue('C2', '10');
        $sheet->setCellValue('D2', '3');
        $sheet->setCellValue('E2', 'Beurer');
        $sheet->setCellValue('F2', 'PO 30');
        $sheet->setCellValue('G2', 'BR-PO-1122');
        $sheet->setCellValue('H2', '2024-08-01');
        $sheet->setCellValue('I2', '850000');
        $sheet->setCellValue('J2', 'Operasional RS');
        $sheet->setCellValue('K2', '2026-08-01');
        $sheet->setCellValue('L2', 'Baik');
        $sheet->setCellValue('M2', 'Aktif');
        $sheet->setCellValue('N2', '0');
        $sheet->setCellValue('O2', 'MED-DIAG');
        $sheet->setCellValue('P2', 'R-IGD-RESUS');
        $sheet->setCellValue('Q2', 'IGD');
        $sheet->setCellValue('R2', 'PT Medika Alkes Indonesia');
        $sheet->setCellValue('S2', 'Pengadaan oximeter cadangan IGD');

        $tempPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'asset_import_test_'.uniqid().'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $service = app(AssetExcelService::class);
        $result = $service->import($tempPath, $user->id);

        @unlink($tempPath);

        $this->assertSame(1, $result['success']);
        $this->assertSame(0, $result['failed']);
        $this->assertDatabaseHas('assets', [
            'asset_code' => 'AST-TEST-999',
            'name' => 'Pulse Oximeter Fingertip Medis',
            'stock' => 10,
            'safety_stock' => 3,
        ]);
    }
}
