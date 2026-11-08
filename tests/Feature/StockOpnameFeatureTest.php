<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\StockOpnames\Pages\ListStockOpnames;
use App\Models\Asset;
use App\Models\StockOpname;
use App\Models\User;
use Database\Seeders\InitialMasterSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleAssetDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StockOpnameFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
    }

    public function test_stock_opname_auto_populates_items_on_creation(): void
    {
        $user = User::first();
        $totalAssets = Asset::count();
        $this->assertGreaterThan(0, $totalAssets);

        $opname = StockOpname::create([
            'opname_number' => 'SO-TEST-001',
            'title' => 'Stock Opname Rumah Sakit Lengkap',
            'start_date' => now(),
            'auditor_id' => $user->id,
            'status' => 'berjalan',
        ]);

        $this->assertGreaterThan(0, $opname->items()->count());
        $this->assertSame($totalAssets, $opname->fresh()->total_expected);
        $this->assertSame(0, $opname->fresh()->total_scanned);
        $this->assertSame(0, $opname->fresh()->total_discrepancy);
    }

    public function test_audit_inventory_records_discrepancy_and_notes(): void
    {
        $user = User::first();
        $asset = Asset::first();

        $opname = StockOpname::create([
            'opname_number' => 'SO-TEST-002',
            'title' => 'Audit Ruangan Khusus',
            'location_id' => $asset->location_id,
            'department_id' => $asset->department_id,
            'start_date' => now(),
            'auditor_id' => $user->id,
            'status' => 'berjalan',
        ]);

        // Audit the asset via Table Action: search asset, tick discrepancy, input quantity & notes
        Livewire::actingAs($user)
            ->test(ListStockOpnames::class)
            ->callTableAction('audit_asset', $opname, [
                'asset_id' => $asset->id,
                'expected_stock' => $asset->stock,
                'actual_stock' => max(0, $asset->stock - 1), // 1 unit missing
                'has_discrepancy' => true,
                'discrepancy_quantity' => -1,
                'root_cause' => 'barang_rusak',
                'notes' => '1 unit mengalami kerusakan total di gudang transit.',
            ])
            ->assertHasNoTableActionErrors();

        $opname->refresh();
        $this->assertSame(1, $opname->total_scanned);
        $this->assertSame(1, $opname->total_discrepancy);
        $this->assertLessThan(100.0, (float) $opname->total_accuracy_rate);

        $item = $opname->items()->where('asset_id', $asset->id)->first();
        $this->assertNotNull($item);
        $this->assertSame('selisih', $item->status);
        $this->assertSame(-1, $item->discrepancy_quantity);
        $this->assertSame('barang_rusak', $item->root_cause);
        $this->assertStringContainsString('1 unit mengalami kerusakan', $item->notes);
    }
}
