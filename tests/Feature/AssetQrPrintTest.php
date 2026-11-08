<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\InitialMasterSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleAssetDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetQrPrintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
    }

    private function createAsset(string $code = 'AST-2026-990001', string $name = 'Ventilator Mekanik ICU'): Asset
    {
        $dept = Department::first();
        $cat = Category::first();
        $loc = Location::first();

        return Asset::create([
            'asset_code' => $code,
            'name' => $name,
            'department_id' => $dept->id,
            'category_id' => $cat->id,
            'location_id' => $loc->id,
            'condition' => 'baik',
            'status' => 'aktif',
            'created_by' => 1,
        ]);
    }

    public function test_guest_cannot_access_qr_print_pages(): void
    {
        $asset = $this->createAsset();

        $this->get(route('assets.print-qr', ['asset' => $asset]))
            ->assertRedirect('/login');

        $this->get(route('assets.print-qr-bulk'))
            ->assertRedirect('/login');
    }

    public function test_asset_qr_scan_text_contains_full_detail_information_for_phone_scanners(): void
    {
        $asset = $this->createAsset('AST-2026-990001', 'Ventilator Emergency');
        $scanText = $asset->getQrScanText();

        $this->assertStringContainsString('SIMAS-RS UMMI BOGOR', $scanText);
        $this->assertStringContainsString('Kode: AST-2026-990001', $scanText);
        $this->assertStringContainsString('Nama: Ventilator Emergency', $scanText);
        $this->assertStringContainsString('Kategori:', $scanText);
        $this->assertStringContainsString('Lokasi:', $scanText);
        $this->assertStringContainsString('Unit:', $scanText);
        $this->assertStringContainsString('Kondisi:', $scanText);
        $this->assertStringContainsString('Status:', $scanText);
    }

    public function test_authenticated_user_can_view_single_compact_sticker_label(): void
    {
        $admin = User::where('email', 'admin@rs-ummi.id')->firstOrFail();
        $asset = $this->createAsset('AST-2026-990001', 'Defibrillator Emergency');

        $response = $this->actingAs($admin)->get(route('assets.print-qr', ['asset' => $asset]));

        $response->assertSuccessful();
        $response->assertSee('AST-2026-990001');
        $response->assertSee('Defibrillator Emergency');
        $response->assertSee('sticker-card-qr');
        $response->assertSee('sticker-card-code');
    }

    public function test_authenticated_user_can_view_bulk_batch_qr_labels(): void
    {
        $petugasAset = User::where('email', 'petugas.aset@rs-ummi.id')->firstOrFail();
        $asset1 = $this->createAsset('AST-2026-990001', 'Syringe Pump ICU');
        $asset2 = $this->createAsset('AST-2026-990002', 'Infusion Pump IGD');

        $url = route('assets.print-qr-bulk', ['ids' => "{$asset1->id},{$asset2->id}"]);
        $response = $this->actingAs($petugasAset)->get($url);

        $response->assertSuccessful();
        $response->assertSee('AST-2026-990001');
        $response->assertSee('Syringe Pump ICU');
        $response->assertSee('AST-2026-990002');
        $response->assertSee('Infusion Pump IGD');
    }

    public function test_filament_batch_print_asset_qr_codes_page_is_accessible(): void
    {
        $admin = User::where('email', 'admin@rs-ummi.id')->firstOrFail();
        $this->createAsset('AST-2026-990001', 'Patient Monitor Bionet');

        $response = $this->actingAs($admin)->get('/admin/print-asset-qr-codes');

        $response->assertSuccessful();
        $response->assertSee('Cetak Batch QR Code Barang');
        $response->assertSee('Modul Cetak');
        $response->assertSee('CARI BARANG / SKU');
        $response->assertSee('MODE PILIHAN CETAK');
        $response->assertSee('PENGURUTAN DATA');
        $response->assertSee('LAYOUT KERTAS / STIKER');
        $response->assertSee('Siap Dicetak:');
        $response->assertSee('Cetak Stiker Single');
        $response->assertSee('AST-2026-990001');
    }

    public function test_non_admin_and_non_petugas_aset_cannot_access_qr_print_features(): void
    {
        $asset = $this->createAsset('AST-2026-990001', 'Patient Monitor Bionet');

        $unauthorizedEmails = [
            'teknisi@rs-ummi.id',
            'kepala.unit@rs-ummi.id',
            'staf.klinis@rs-ummi.id',
            'manajemen@rs-ummi.id',
        ];

        foreach ($unauthorizedEmails as $email) {
            $this->flushSession();
            $user = User::where('email', $email)->firstOrFail();

            // 1. Batch print page di Filament
            $this->actingAs($user)
                ->get('/admin/print-asset-qr-codes')
                ->assertForbidden();

            // 2. Endpoint cetak stiker single
            $this->actingAs($user)
                ->get(route('assets.print-qr', ['asset' => $asset]))
                ->assertForbidden();

            // 3. Endpoint cetak stiker bulk
            $this->actingAs($user)
                ->get(route('assets.print-qr-bulk', ['ids' => (string) $asset->id]))
                ->assertForbidden();
        }
    }
}
