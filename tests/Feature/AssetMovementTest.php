<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AssetMovementStatus;
use App\Filament\Resources\AssetMovements\Pages\CreateAssetMovement;
use App\Filament\Resources\AssetMovements\Pages\ListAssetMovements;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\InitialMasterSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleAssetDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssetMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_movement_list_renders_successfully(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        Livewire::actingAs($user)
            ->test(ListAssetMovements::class)
            ->assertSuccessful()
            ->assertSee('Waktu Mutasi')
            ->assertSee('Ruangan Asal')
            ->assertSee('Ruangan Tujuan');
    }

    public function test_asset_movement_can_be_approved_and_updates_asset_location(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        $movement = AssetMovement::where('status', AssetMovementStatus::MENUNGGU_PERSETUJUAN)->firstOrFail();
        $asset = $movement->asset;
        $targetLocationId = $movement->to_location_id;
        $targetDepartmentId = $movement->to_department_id;

        Livewire::actingAs($user)
            ->test(ListAssetMovements::class)
            ->callTableAction('approve', $movement)
            ->assertHasNoTableActionErrors();

        $movement->refresh();
        $asset->refresh();

        $this->assertSame(AssetMovementStatus::DISETUJUI, $movement->status);
        $this->assertSame($user->id, $movement->approved_by);
        $this->assertSame($targetLocationId, $asset->location_id);
        $this->assertSame($targetDepartmentId, $asset->department_id);
    }

    public function test_asset_movement_can_be_rejected_with_reason(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        $movement = AssetMovement::where('status', AssetMovementStatus::MENUNGGU_PERSETUJUAN)->firstOrFail();

        Livewire::actingAs($user)
            ->test(ListAssetMovements::class)
            ->callTableAction('reject', $movement, [
                'rejection_reason' => 'Ruang tujuan sedang dilakukan dekontaminasi.',
            ])
            ->assertHasNoTableActionErrors();

        $movement->refresh();

        $this->assertSame(AssetMovementStatus::DITOLAK, $movement->status);
        $this->assertSame('Ruang tujuan sedang dilakukan dekontaminasi.', $movement->rejection_reason);
    }

    public function test_create_asset_movement_validates_same_location(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        $asset = Asset::firstOrFail();

        Livewire::actingAs($user)
            ->test(CreateAssetMovement::class)
            ->fillForm([
                'asset_id' => $asset->id,
                'from_location_id' => $asset->location_id,
                'to_location_id' => $asset->location_id, // Same location!
                'from_department_id' => $asset->department_id,
                'to_department_id' => $asset->department_id,
                'status' => AssetMovementStatus::MENUNGGU_PERSETUJUAN->value,
                'moved_at' => now()->toDateTimeString(),
                'moved_by' => $user->id,
                'reason' => 'Uji coba mutasi ke lokasi yang sama.',
            ])
            ->call('create')
            ->assertHasFormErrors(['to_location_id']);
    }
}
