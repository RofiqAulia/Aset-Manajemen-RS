<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Calibration;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Jumlah Stok Inventaris (beserta perbandingan stok baik dan rusak)
        $totalStock = (int) Asset::sum('stock');
        $goodStock = (int) Asset::where('condition', AssetCondition::BAIK)->sum('stock');
        $brokenStock = (int) Asset::where('condition', AssetCondition::RUSAK)->sum('stock');

        // 2. Jumlah Stok Habis (beserta perbandingan stok menipis)
        $outOfStockCount = Asset::where('stock', '<=', 0)->count();
        $lowStockCount = Asset::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'safety_stock')
            ->count();

        // 3. Persentase Alat Penunjang Hidup Kritis dalam Kondisi Aktif dan Baik
        $criticalTotal = Asset::where('is_critical', true)->count();
        $criticalReady = Asset::where('is_critical', true)
            ->where('condition', AssetCondition::BAIK)
            ->where('status', AssetStatus::AKTIF)
            ->count();
        $criticalPercent = $criticalTotal > 0 ? (int) round(($criticalReady / $criticalTotal) * 100) : 100;

        // 4. Jumlah Alat yang Mendekati Kepatuhan Kalibrasi <= 30 Hari
        $nearCalibrationCount = Calibration::whereDate('expiry_date', '<=', now()->addDays(30))->count();

        return [
            Stat::make('Jumlah Stok Inventaris', "{$totalStock} Unit")
                ->description("{$goodStock} Unit Baik | {$brokenStock} Unit Rusak")
                ->descriptionIcon(Heroicon::OutlinedCube)
                ->color($brokenStock > 0 ? 'warning' : 'success')
                ->chart([$brokenStock, (int) ($totalStock / 2), $goodStock, $totalStock]),

            Stat::make('Jumlah Stok Habis', "{$outOfStockCount} Aset")
                ->description("{$lowStockCount} Aset Menipis (<= Batas Minimum)")
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color(($outOfStockCount > 0 || $lowStockCount > 0) ? 'danger' : 'success')
                ->chart([$lowStockCount, $outOfStockCount, $lowStockCount + $outOfStockCount]),

            Stat::make('Kesiapan Alat Penunjang Hidup', "{$criticalPercent}%")
                ->description("{$criticalReady} dari {$criticalTotal} Unit Kritis Aktif & Baik")
                ->descriptionIcon(Heroicon::OutlinedShieldCheck)
                ->color($criticalPercent >= 90 ? 'success' : 'danger')
                ->chart([85, 90, 92, 95, $criticalPercent]),

            Stat::make('Mendekati Kepatuhan Kalibrasi', "{$nearCalibrationCount} Alat")
                ->description('Jatuh Tempo <= 30 Hari / Perlu Tindak Lanjut')
                ->descriptionIcon(Heroicon::OutlinedCheckBadge)
                ->color($nearCalibrationCount > 0 ? 'warning' : 'success')
                ->chart([0, 1, 1, 2, $nearCalibrationCount]),
        ];
    }
}
