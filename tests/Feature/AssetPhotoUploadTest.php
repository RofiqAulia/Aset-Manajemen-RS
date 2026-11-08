<?php

namespace Tests\Feature;

use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Schemas\AssetForm;
use App\Models\Asset;
use App\Models\User;
use Database\Seeders\InitialMasterSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleAssetDataSeeder;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AssetPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_photo_upload_configured_with_public_disk_and_camera_action(): void
    {
        $schema = Schema::make();
        $configured = AssetForm::configure($schema);

        $components = $configured->getComponents();
        $photoField = null;

        foreach ($components as $component) {
            if ($component instanceof FileUpload && $component->getName() === 'photo_url') {
                $photoField = $component;
                break;
            }
        }

        $this->assertNotNull($photoField, 'Komponen photo_url harus berupa FileUpload.');
        $this->assertSame('public', $photoField->getDiskName());
        $this->assertSame('assets/photos', $photoField->getDirectory());

        $hintActions = $photoField->getHintActions();
        $this->assertNotEmpty($hintActions, 'FileUpload harus memiliki hintAction untuk kamera.');
        $cameraAction = $hintActions[0] ?? null;
        $this->assertNotNull($cameraAction);
        $this->assertSame('captureFromCamera', $cameraAction->getName());
        $this->assertSame('Buka Kamera Langsung', $cameraAction->getLabel());
    }

    public function test_public_disk_url_is_relative_to_prevent_cross_origin_hang(): void
    {
        $url = Storage::disk('public')->url('assets/photos/test.jpg');
        $this->assertSame('/storage/assets/photos/test.jpg', $url);
    }

    public function test_edit_asset_form_loads_existing_photo_successfully(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);

        $user = User::first();
        $asset = Asset::first();

        // Ensure storage directory and dummy file exist
        Storage::disk('public')->put('assets/photos/sample-asset.jpg', 'fake-image-content');
        $asset->update(['photo_url' => 'assets/photos/sample-asset.jpg']);

        Livewire::actingAs($user)
            ->test(EditAsset::class, ['record' => $asset->id])
            ->assertSuccessful()
            ->assertSchemaStateSet([
                'photo_url' => 'assets/photos/sample-asset.jpg',
            ]);
    }
}
