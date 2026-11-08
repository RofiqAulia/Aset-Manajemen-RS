<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Widgets\DailyActivityLogWidget;
use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use Database\Seeders\InitialMasterSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleAssetDataSeeder;
use Filament\Tables\Enums\PaginationMode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_overview_widget_renders_expected_metrics(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        Livewire::actingAs($user)
            ->test(StatsOverview::class)
            ->assertSuccessful()
            ->assertSee('Jumlah Stok Inventaris')
            ->assertSee('Jumlah Stok Habis')
            ->assertSee('Kesiapan Alat Penunjang Hidup')
            ->assertSee('Mendekati Kepatuhan Kalibrasi');
    }

    public function test_daily_activity_log_widget_renders_successfully(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        Livewire::actingAs($user)
            ->test(DailyActivityLogWidget::class)
            ->assertSuccessful()
            ->assertSee('Log Aktivitas Harian Pemeliharaan Aset')
            ->assertSee('Cetak Laporan PDF')
            ->callTableAction('export_pdf')
            ->assertFileDownloaded();
    }

    public function test_daily_activity_log_widget_pagination_mode_and_pages(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
        $this->seed(SampleAssetDataSeeder::class);
        $user = User::first();

        $component = Livewire::actingAs($user)
            ->test(DailyActivityLogWidget::class)
            ->assertSuccessful();

        $table = $component->instance()->getTable();
        $this->assertSame(PaginationMode::Default, $table->getPaginationMode());
        $this->assertSame(5, $table->getDefaultPaginationPageOption());
    }
}
