<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Models\Asset;
use App\Models\User;
use Database\Seeders\InitialMasterSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleAssetDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AutoRedirectAfterActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
    }

    public function test_admin_panel_has_global_redirect_configured(): void
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        $this->assertSame('index', $panel->getResourceCreatePageRedirect());
        $this->assertSame('index', $panel->getResourceEditPageRedirect());
    }

    public function test_create_asset_redirects_to_asset_index_table(): void
    {
        $user = User::first();
        $asset = Asset::first();

        Livewire::actingAs($user)
            ->test(CreateAsset::class)
            ->fillForm([
                'asset_code' => 'TEST-AUTO-REDIRECT-001',
                'name' => 'Alat Medis Otomatis Redirect',
                'stock' => 10,
                'safety_stock' => 2,
                'condition' => 'baik',
                'status' => 'aktif',
                'is_critical' => false,
                'category_id' => $asset->category_id,
                'location_id' => $asset->location_id,
                'department_id' => $asset->department_id,
                'created_by' => $user->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(ListAssets::getUrl());

        $this->assertDatabaseHas('assets', [
            'asset_code' => 'TEST-AUTO-REDIRECT-001',
            'name' => 'Alat Medis Otomatis Redirect',
        ]);
    }

    public function test_edit_asset_redirects_to_asset_index_table(): void
    {
        $user = User::first();
        $asset = Asset::first();

        Livewire::actingAs($user)
            ->test(EditAsset::class, ['record' => $asset->id])
            ->fillForm([
                'name' => 'Nama Aset Telah Diperbarui',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(ListAssets::getUrl());

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'Nama Aset Telah Diperbarui',
        ]);
    }

    public function test_delete_asset_from_edit_page_redirects_to_asset_index_table(): void
    {
        $user = User::first();
        $asset = Asset::first();

        Livewire::actingAs($user)
            ->test(EditAsset::class, ['record' => $asset->id])
            ->callAction('delete')
            ->assertRedirect(ListAssets::getUrl());

        $this->assertSoftDeleted('assets', [
            'id' => $asset->id,
        ]);
    }
}
