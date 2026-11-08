<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\AssetMovements\Pages\ListAssetMovements;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Filament\Resources\Calibrations\Pages\ListCalibrations;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Departments\Pages\ListDepartments;
use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Filament\Resources\MaintenanceSchedules\Pages\ListMaintenanceSchedules;
use App\Filament\Resources\StockOpnames\Pages\ListStockOpnames;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Vendors\Pages\ListVendors;
use App\Filament\Resources\WorkOrders\Pages\ListWorkOrders;
use App\Filament\Widgets\DailyActivityLogWidget;
use App\Models\User;
use Database\Seeders\InitialMasterSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleAssetDataSeeder;
use Filament\Tables\Enums\PaginationMode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GlobalPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_tables_have_default_pagination_mode_and_five_per_page(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        $pageClasses = [
            ListAssets::class,
            ListAssetMovements::class,
            ListCalibrations::class,
            ListCategories::class,
            ListDepartments::class,
            ListLocations::class,
            ListMaintenanceSchedules::class,
            ListStockOpnames::class,
            ListUsers::class,
            ListVendors::class,
            ListWorkOrders::class,
            DailyActivityLogWidget::class,
        ];

        foreach ($pageClasses as $pageClass) {
            $component = Livewire::actingAs($user)->test($pageClass)->assertSuccessful();
            $table = $component->instance()->getTable();

            $this->assertSame(
                PaginationMode::Default,
                $table->getPaginationMode(),
                "Table for {$pageClass} does not use PaginationMode::Default"
            );
            $this->assertSame(
                5,
                $table->getDefaultPaginationPageOption(),
                "Table for {$pageClass} does not have default 5 records per page"
            );
            $this->assertSame(
                [5, 10, 25, 50],
                $table->getPaginationPageOptions(),
                "Table for {$pageClass} does not have [5, 10, 25, 50] page options"
            );
        }
    }
}
