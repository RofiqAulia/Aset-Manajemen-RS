<?php

namespace App\Filament\Resources\StockOpnames\Schemas;

use App\Models\StockOpname;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockOpnameInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('1. Ringkasan Eksekusi (Progress Dashboard)')
                    ->description('Pemantauan berjalannya proses stock opname dan progres fisik inventaris.')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible()
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('physical_progress_rate')
                                ->label('Progres Fisik Selesai')
                                ->suffix('%')
                                ->badge()
                                ->color(fn (float $state): string => $state >= 100 ? 'success' : ($state > 0 ? 'info' : 'gray')),
                            TextEntry::make('total_expected')
                                ->label('Total Terdaftar di Sistem')
                                ->suffix(' Aset')
                                ->numeric()
                                ->weight('bold'),
                            TextEntry::make('total_scanned')
                                ->label('Total Terverifikasi Fisik')
                                ->suffix(' Aset')
                                ->numeric()
                                ->badge()
                                ->color('info'),
                            TextEntry::make('remaining_items')
                                ->label('Sisa Belum Dihitung')
                                ->state(fn (StockOpname $record): int => max(0, $record->total_expected - $record->total_scanned))
                                ->suffix(' Aset')
                                ->badge()
                                ->color(fn (int $state): string => $state === 0 ? 'success' : 'warning'),
                        ]),
                        Grid::make(4)->schema([
                            TextEntry::make('opname_number')
                                ->label('Nomor Stock Opname'),
                            TextEntry::make('title')
                                ->label('Judul Kegiatan'),
                            TextEntry::make('auditor.name')
                                ->label('Auditor / Ketua Tim'),
                            TextEntry::make('status')
                                ->label('Status Pelaksanaan')
                                ->badge()
                                ->formatStateUsing(fn (?string $state): string => match ($state) {
                                    'berjalan' => 'Berjalan',
                                    'selesai' => 'Selesai',
                                    'dibatalkan' => 'Dibatalkan',
                                    default => ucfirst((string) $state),
                                })
                                ->color(fn (?string $state): string => match ($state) {
                                    'berjalan' => 'info',
                                    'selesai' => 'success',
                                    'dibatalkan' => 'danger',
                                    default => 'gray',
                                }),
                        ]),
                    ]),

                Section::make('2. Varians dan Selisih (Discrepancy & Variance Dashboard)')
                    ->description('Pendeteksian selisih akurasi inventaris antara sistem dengan kondisi fisik.')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('total_accuracy_rate')
                                ->label('Tingkat Akurasi Stok (Accuracy Rate)')
                                ->suffix('%')
                                ->badge()
                                ->color(fn (float $state): string => $state >= 95 ? 'success' : ($state >= 85 ? 'warning' : 'danger'))
                                ->helperText('Target akurasi ideal manajemen rumah sakit: >95-99%'),
                            TextEntry::make('total_discrepancy')
                                ->label('Jumlah Item Berselisih')
                                ->suffix(' Aset')
                                ->badge()
                                ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success'),
                            TextEntry::make('total_variance_value')
                                ->label('Total Nilai Finansial Selisih (Rp)')
                                ->money('IDR', locale: 'id')
                                ->badge()
                                ->color(fn (float $state): string => $state != 0.0 ? 'danger' : 'success')
                                ->helperText('Kerugian deviasi fisik barang hilang/kurang atau selisih lebih.'),
                        ]),
                        TextEntry::make('discrepant_summary')
                            ->label('Daftar Barang yang Ditemukan Selisih')
                            ->state(function (StockOpname $record): string {
                                $items = $record->items()
                                    ->where('status', 'selisih')
                                    ->with('asset')
                                    ->get();

                                if ($items->isEmpty()) {
                                    return 'Tidak ada barang berselisih. Seluruh inventaris fisik yang diperiksa cocok 100% dengan data sistem.';
                                }

                                return $items->map(function ($item, $idx) {
                                    $num = $idx + 1;
                                    $code = $item->asset?->asset_code ?? '-';
                                    $name = $item->asset?->name ?? 'Aset';
                                    $exp = $item->expected_stock;
                                    $act = $item->actual_stock ?? 0;
                                    $diff = $item->discrepancy_quantity > 0 ? "+{$item->discrepancy_quantity}" : "{$item->discrepancy_quantity}";
                                    $val = number_format((float) $item->discrepancy_value, 0, ',', '.');
                                    $cause = match ($item->root_cause) {
                                        'salah_input' => 'Salah Input Sistem',
                                        'barang_rusak' => 'Barang Rusak / Afkir',
                                        'salah_lokasi' => 'Salah Taruh Lokasi',
                                        'hilang_pencurian' => 'Hilang / Penyusutan',
                                        'selisih_fisik' => 'Selisih Fisik Gudang',
                                        default => $item->root_cause ?? 'Belum Ditentukan',
                                    };
                                    $note = $item->notes ? " | Ket: {$item->notes}" : '';

                                    return "{$num}. [{$code}] {$name} | Sistem: {$exp} | Fisik: {$act} | Selisih: {$diff} unit (Rp {$val}) | Penyebab: {$cause}{$note}";
                                })->implode("\n");
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('3. Analisis Akar Masalah & Area (Root Cause & Area Dashboard)')
                    ->description('Evaluasi penyebab selisih dan pemetaan unit/ruangan audit.')
                    ->icon('heroicon-o-magnifying-glass')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('location.name')
                                ->label('Ruangan / Lokasi Diaudit')
                                ->placeholder('Seluruh Ruangan Rumah Sakit'),
                            TextEntry::make('department.name')
                                ->label('Unit / Departemen')
                                ->placeholder('Seluruh Departemen'),
                            TextEntry::make('root_causes_breakdown')
                                ->label('Distribusi Penyebab Selisih')
                                ->state(function (StockOpname $record): string {
                                    $causes = $record->items()
                                        ->where('status', 'selisih')
                                        ->whereNotNull('root_cause')
                                        ->selectRaw('root_cause, count(*) as count')
                                        ->groupBy('root_cause')
                                        ->pluck('count', 'root_cause');

                                    if ($causes->isEmpty()) {
                                        return 'Nihil temuan selisih.';
                                    }

                                    return $causes->map(function ($count, $cause) {
                                        $label = match ($cause) {
                                            'salah_input' => 'Salah Input',
                                            'barang_rusak' => 'Barang Rusak',
                                            'salah_lokasi' => 'Salah Lokasi',
                                            'hilang_pencurian' => 'Hilang',
                                            'selisih_fisik' => 'Selisih Fisik',
                                            default => ucfirst((string) $cause),
                                        };

                                        return "{$label}: {$count} item";
                                    })->implode(' | ');
                                }),
                        ]),
                        TextEntry::make('notes')
                            ->label('Catatan Tambahan & Rekomendasi Auditor')
                            ->placeholder('Belum ada catatan evaluasi.')
                            ->columnSpanFull(),
                    ]),

                Section::make('4. Nilai & Kesehatan Stok (Inventory Valuation Dashboard)')
                    ->description('Evaluasi total nilai aset fisik riil pasca opname dan penyusutan nilai.')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('actual_total_valuation')
                                ->label('Total Nilai Stok Fisik Aktual')
                                ->state(function (StockOpname $record): string {
                                    $total = $record->items()
                                        ->with('asset')
                                        ->get()
                                        ->sum(fn ($item) => ($item->actual_stock ?? $item->expected_stock) * (float) ($item->asset?->acquisition_cost ?? 0));

                                    return 'Rp '.number_format($total, 0, ',', '.');
                                })
                                ->badge()
                                ->color('success')
                                ->helperText('Kalkulasi nilai aset dari kuantitas fisik riil.'),
                            TextEntry::make('dead_stock_estimate')
                                ->label('Aset Rusak / Dead Stock Ditemukan')
                                ->state(function (StockOpname $record): string {
                                    $count = $record->items()
                                        ->where(fn ($q) => $q->where('actual_condition', 'rusak_berat')->orWhere('root_cause', 'barang_rusak'))
                                        ->count();

                                    return "{$count} Aset Rusak / Afkir";
                                })
                                ->badge()
                                ->color('warning'),
                            TextEntry::make('shrinkage_rate')
                                ->label('Penyusutan Nilai (Shrinkage Rate)')
                                ->state(function (StockOpname $record): string {
                                    $totalCost = $record->items()->with('asset')->get()->sum(fn ($i) => $i->expected_stock * (float) ($i->asset?->acquisition_cost ?? 0));
                                    $loss = abs((float) $record->total_variance_value);
                                    if ($totalCost <= 0 || $loss <= 0) {
                                        return '0.00%';
                                    }
                                    $rate = round(($loss / $totalCost) * 100, 2);

                                    return "{$rate}%";
                                })
                                ->badge()
                                ->color(fn (string $state): string => $state === '0.00%' ? 'success' : 'danger'),
                        ]),
                    ]),
            ]);
    }
}
