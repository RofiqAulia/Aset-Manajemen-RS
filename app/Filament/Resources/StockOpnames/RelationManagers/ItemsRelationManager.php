<?php

declare(strict_types=1);

namespace App\Filament\Resources\StockOpnames\RelationManagers;

use App\Enums\AssetCondition;
use App\Enums\StockOpnameResult;
use App\Models\Asset;
use App\Models\Location;
use App\Models\StockOpnameItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Rincian Pemeriksaan Fisik & Selisih Aset';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('asset.name')
            ->columns([
                TextColumn::make('asset.asset_code')
                    ->label('Kode Aset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.name')
                    ->label('Nama Aset')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('expected_stock')
                    ->label('Stok Sistem')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),
                TextColumn::make('actual_stock')
                    ->label('Stok Fisik')
                    ->numeric()
                    ->badge()
                    ->color(fn (?int $state): string => $state === null ? 'warning' : 'info')
                    ->formatStateUsing(fn (?int $state): string => $state === null ? 'Belum Dihitung' : (string) $state)
                    ->alignCenter(),
                TextColumn::make('discrepancy_quantity')
                    ->label('Selisih Fisik')
                    ->numeric()
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "+{$state}" : (string) $state)
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'success',
                        $state < 0 => 'danger',
                        default => 'warning',
                    })
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('discrepancy_value')
                    ->label('Nilai Selisih (Rp)')
                    ->money('IDR', locale: 'id')
                    ->color(fn (float $state): string => $state != 0.0 ? 'danger' : 'gray')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Cek')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'sesuai' => 'Sesuai',
                        'selisih' => 'Ada Selisih',
                        'hitung_ulang' => 'Hitung Ulang',
                        default => 'Belum Diperiksa',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'sesuai' => 'success',
                        'selisih' => 'danger',
                        'hitung_ulang' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('root_cause')
                    ->label('Akar Masalah')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'salah_input' => 'Salah Input Sistem',
                        'barang_rusak' => 'Barang Rusak / Afkir',
                        'salah_lokasi' => 'Salah Taruh Lokasi',
                        'hilang_pencurian' => 'Hilang / Penyusutan',
                        'selisih_fisik' => 'Selisih Fisik Gudang',
                        'lainnya' => 'Lainnya',
                        default => $state ? ucfirst($state) : '-',
                    })
                    ->color('gray')
                    ->placeholder('-'),
                TextColumn::make('notes')
                    ->label('Keterangan Selisih')
                    ->placeholder('-')
                    ->wrap()
                    ->color(fn (StockOpnameItem $record): string => $record->status === 'selisih' ? 'danger' : 'gray'),
                TextColumn::make('expectedLocation.name')
                    ->label('Lokasi Sistem')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('actualLocation.name')
                    ->label('Lokasi Fisik')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('scanned_at')
                    ->label('Waktu Periksa')
                    ->dateTime()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'sesuai' => 'Sesuai',
                        'selisih' => 'Ada Selisih',
                        'hitung_ulang' => 'Butuh Hitung Ulang',
                        'belum_diperiksa' => 'Belum Diperiksa',
                    ]),
                SelectFilter::make('root_cause')
                    ->label('Filter Akar Masalah')
                    ->options([
                        'salah_input' => 'Salah Input Sistem',
                        'barang_rusak' => 'Barang Rusak',
                        'salah_lokasi' => 'Salah Lokasi',
                        'hilang_pencurian' => 'Hilang / Pencurian',
                        'selisih_fisik' => 'Selisih Hitung Fisik',
                    ]),
            ])
            ->headerActions([
                Action::make('audit_inventory')
                    ->label('Cari Barang di Inventaris & Catat Selisih')
                    ->icon('heroicon-o-magnifying-glass-plus')
                    ->color('primary')
                    ->modalHeading('Pencarian Inventaris & Pencatatan Selisih Fisik')
                    ->modalDescription('Cari barang di data inventaris, tentukan hasil penghitungan fisik, centang jika terdapat selisih, dan berikan keterangan.')
                    ->modalSubmitActionLabel('Simpan Hasil Pemeriksaan')
                    ->modalWidth('xl')
                    ->form([
                        Select::make('asset_id')
                            ->label('Cari Barang di Data Inventaris')
                            ->options(function (): array {
                                $parent = $this->getOwnerRecord();
                                $query = Asset::query();
                                if ($parent->location_id) {
                                    $query->where('location_id', $parent->location_id);
                                }
                                if ($parent->department_id) {
                                    $query->where('department_id', $parent->department_id);
                                }
                                if ($query->count() === 0) {
                                    $query = Asset::query();
                                }

                                return $query->get()->mapWithKeys(fn (Asset $asset) => [
                                    $asset->id => "[{$asset->asset_code}] {$asset->name} (Stok Sistem: {$asset->stock})",
                                ])->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set): void {
                                if ($asset = Asset::find($state)) {
                                    $stock = (int) ($asset->stock ?? 1);
                                    $set('expected_stock', $stock);
                                    $set('actual_stock', $stock);
                                    $set('has_discrepancy', false);
                                    $set('discrepancy_quantity', 0);
                                }
                            }),
                        TextInput::make('expected_stock')
                            ->label('Stok Menurut Data Sistem')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('actual_stock')
                            ->label('Jumlah Fisik Riil Ditemukan')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                $expected = (int) $get('expected_stock');
                                $actual = (int) $state;
                                $diff = $actual - $expected;
                                $set('discrepancy_quantity', $diff);
                                $set('has_discrepancy', $diff !== 0);
                            }),
                        Toggle::make('has_discrepancy')
                            ->label('Centang Jika Terdapat Selisih Fisik')
                            ->helperText('Aktifkan jika jumlah fisik berbeda dengan data sistem atau ada catatan/temuan khusus.')
                            ->live(),
                        TextInput::make('discrepancy_quantity')
                            ->label('Jumlah Selisih Fisik')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->prefix('Selisih:')
                            ->suffix('unit')
                            ->helperText('Nilai minus (-) berarti barang kurang/hilang. Nilai plus (+) berarti barang lebih fisik.'),
                        Select::make('root_cause')
                            ->label('Kategori Penyebab / Akar Masalah Selisih')
                            ->options([
                                'salah_input' => 'Salah Input Data Sistem',
                                'barang_rusak' => 'Barang Rusak / Afkir',
                                'salah_lokasi' => 'Salah Penempatan Lokasi / Rak',
                                'hilang_pencurian' => 'Hilang / Indikasi Pencurian',
                                'selisih_fisik' => 'Selisih Penghitungan Fisik Gudang',
                                'lainnya' => 'Lainnya',
                            ])
                            ->visible(fn (callable $get): bool => (bool) $get('has_discrepancy') || (int) $get('discrepancy_quantity') !== 0)
                            ->required(fn (callable $get): bool => (bool) $get('has_discrepancy') || (int) $get('discrepancy_quantity') !== 0),
                        Textarea::make('notes')
                            ->label('Keterangan Selisih / Penjelasan Hasil Pemeriksaan')
                            ->placeholder('Tuliskan rincian penjelasan selisih (contoh: 1 unit rusak di ruang transit, 2 unit belum terinput mutasi)...')
                            ->required(fn (callable $get): bool => (bool) $get('has_discrepancy') || (int) $get('discrepancy_quantity') !== 0)
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data): void {
                        $parent = $this->getOwnerRecord();
                        $asset = Asset::findOrFail($data['asset_id']);
                        $expectedStock = (int) ($asset->stock ?? 1);
                        $actualStock = (int) $data['actual_stock'];
                        $diff = $actualStock - $expectedStock;
                        $unitCost = (float) ($asset->acquisition_cost ?? 0);
                        $diffValue = $diff * $unitCost;
                        $hasDiscrepancy = (bool) ($data['has_discrepancy'] ?? false) || ($diff !== 0);

                        $item = $parent->items()->firstOrNew(['asset_id' => $asset->id]);
                        $item->expected_stock = $expectedStock;
                        $item->actual_stock = $actualStock;
                        $item->discrepancy_quantity = $diff;
                        $item->discrepancy_value = $diffValue;
                        $item->status = $hasDiscrepancy ? 'selisih' : 'sesuai';
                        $item->root_cause = $hasDiscrepancy ? ($data['root_cause'] ?? 'selisih_fisik') : null;
                        $item->notes = $data['notes'] ?? null;
                        $item->expected_location_id = $item->expected_location_id ?? $asset->location_id;
                        $item->actual_location_id = $item->actual_location_id ?? $asset->location_id;
                        $item->expected_condition = $item->expected_condition ?? ($asset->condition?->value ?? 'baik');
                        $item->actual_condition = $item->actual_condition ?? ($asset->condition?->value ?? 'baik');
                        $item->scanned_at = now();
                        $item->result = match (true) {
                            $diff < 0 && $actualStock === 0 => StockOpnameResult::TIDAK_DITEMUKAN,
                            $diff !== 0 => StockOpnameResult::KONDISI_BERBEDA,
                            default => StockOpnameResult::DITEMUKAN,
                        };
                        $item->save();

                        $parent->recalculateMetrics();

                        Notification::make()
                            ->title($hasDiscrepancy ? 'Tercatat Selisih Barang' : 'Barang Diverifikasi Sesuai')
                            ->body("Aset [{$asset->name}] berhasil diperiksa. Selisih: {$diff} unit.".($data['notes'] ? " Keterangan: {$data['notes']}" : ''))
                            ->color($hasDiscrepancy ? 'warning' : 'success')
                            ->send();
                    }),
                Action::make('mark_all_matched')
                    ->label('Tandai Sisa Barang Sebagai Sesuai')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Verifikasi Massal Sesuai')
                    ->modalDescription('Apakah Anda yakin ingin menandai seluruh barang yang belum dihitung agar statusnya menjadi Sesuai dengan stok sistem?')
                    ->action(function (): void {
                        $parent = $this->getOwnerRecord();
                        $items = $parent->items()->whereNull('actual_stock')->get();

                        foreach ($items as $item) {
                            $item->update([
                                'actual_stock' => $item->expected_stock,
                                'discrepancy_quantity' => 0,
                                'discrepancy_value' => 0,
                                'status' => 'sesuai',
                                'result' => StockOpnameResult::DITEMUKAN,
                                'scanned_at' => now(),
                            ]);
                        }

                        $parent->recalculateMetrics();

                        Notification::make()
                            ->title('Verifikasi Selesai')
                            ->body('Seluruh barang sisa berhasil ditandai sebagai sesuai.')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Cek Fisik')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    ->modalHeading('Pemeriksaan Fisik & Validasi Aset')
                    ->modalDescription('Periksa jumlah fisik riil di gudang/ruangan, kondisi, dan lokasi keberadaan barang.')
                    ->modalSubmitActionLabel('Simpan Hasil Pemeriksaan')
                    ->fillForm(fn (StockOpnameItem $record): array => [
                        'expected_stock' => $record->expected_stock,
                        'actual_stock' => $record->actual_stock ?? $record->expected_stock,
                        'has_discrepancy' => $record->status === 'selisih' || ($record->actual_stock !== null && $record->actual_stock != $record->expected_stock),
                        'discrepancy_quantity' => $record->discrepancy_quantity,
                        'actual_location_id' => $record->actual_location_id ?? $record->expected_location_id,
                        'actual_condition' => $record->actual_condition ?? $record->expected_condition,
                        'root_cause' => $record->root_cause,
                        'notes' => $record->notes,
                    ])
                    ->form([
                        TextInput::make('expected_stock')
                            ->label('Stok Menurut Data Sistem')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('actual_stock')
                            ->label('Jumlah Stok Fisik Ditemukan')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                $expected = (int) $get('expected_stock');
                                $actual = (int) $state;
                                $diff = $actual - $expected;
                                $set('discrepancy_quantity', $diff);
                                $set('has_discrepancy', $diff !== 0);
                            }),
                        Toggle::make('has_discrepancy')
                            ->label('Centang Jika Terdapat Selisih Fisik')
                            ->helperText('Aktifkan jika jumlah fisik berbeda dengan data sistem atau ada catatan khusus.')
                            ->live(),
                        TextInput::make('discrepancy_quantity')
                            ->label('Jumlah Selisih Fisik')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->prefix('Selisih:')
                            ->suffix('unit'),
                        Select::make('actual_condition')
                            ->label('Kondisi Fisik Aktual')
                            ->options(AssetCondition::class)
                            ->required(),
                        Select::make('actual_location_id')
                            ->label('Lokasi Fisik Ditemukan')
                            ->options(Location::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('root_cause')
                            ->label('Akar Masalah (Penyebab Selisih)')
                            ->options([
                                'salah_input' => 'Salah Input Data Sistem',
                                'barang_rusak' => 'Barang Rusak / Afkir',
                                'salah_lokasi' => 'Salah Penempatan Lokasi / Rak',
                                'hilang_pencurian' => 'Hilang / Indikasi Pencurian',
                                'selisih_fisik' => 'Selisih Penghitungan Fisik Gudang',
                                'lainnya' => 'Lainnya',
                            ])
                            ->visible(fn (callable $get): bool => (bool) $get('has_discrepancy') || (int) $get('discrepancy_quantity') !== 0)
                            ->required(fn (callable $get): bool => (bool) $get('has_discrepancy') || (int) $get('discrepancy_quantity') !== 0),
                        Textarea::make('notes')
                            ->label('Keterangan Selisih / Penjelasan')
                            ->placeholder('Tuliskan keterangan detail selisih...')
                            ->required(fn (callable $get): bool => (bool) $get('has_discrepancy') || (int) $get('discrepancy_quantity') !== 0)
                            ->columnSpanFull(),
                    ])
                    ->action(function (StockOpnameItem $record, array $data): void {
                        $actualStock = (int) $data['actual_stock'];
                        $expectedStock = (int) $record->expected_stock;
                        $diff = $actualStock - $expectedStock;
                        $unitCost = (float) ($record->asset?->acquisition_cost ?? 0);
                        $diffValue = $diff * $unitCost;

                        $isLocationMismatch = $data['actual_location_id'] && ((int) $data['actual_location_id'] !== (int) $record->expected_location_id);
                        $isConditionMismatch = $data['actual_condition'] && ($data['actual_condition'] !== $record->expected_condition);
                        $hasDiscrepancy = ($diff !== 0) || $isLocationMismatch || $isConditionMismatch || (bool) ($data['has_discrepancy'] ?? false);

                        $status = $hasDiscrepancy ? 'selisih' : 'sesuai';
                        $result = match (true) {
                            $diff < 0 && $actualStock === 0 => StockOpnameResult::TIDAK_DITEMUKAN,
                            $diff !== 0 => StockOpnameResult::KONDISI_BERBEDA,
                            $isLocationMismatch => StockOpnameResult::LOKASI_BERBEDA,
                            $isConditionMismatch => StockOpnameResult::KONDISI_BERBEDA,
                            default => StockOpnameResult::DITEMUKAN,
                        };

                        $record->update([
                            'actual_stock' => $actualStock,
                            'actual_location_id' => $data['actual_location_id'],
                            'actual_condition' => $data['actual_condition'],
                            'discrepancy_quantity' => $diff,
                            'discrepancy_value' => $diffValue,
                            'status' => $status,
                            'result' => $result,
                            'root_cause' => $hasDiscrepancy ? ($data['root_cause'] ?? 'selisih_fisik') : null,
                            'scanned_at' => now(),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        $record->stockOpname->recalculateMetrics();

                        Notification::make()
                            ->title($hasDiscrepancy ? 'Terdeteksi Selisih Aset' : 'Aset Terverifikasi Sesuai')
                            ->body($hasDiscrepancy ? "Selisih fisik: {$diff} unit (Deviasi: Rp ".number_format($diffValue, 0, ',', '.').')' : 'Stok fisik, lokasi, dan kondisi cocok dengan data sistem.')
                            ->color($hasDiscrepancy ? 'warning' : 'success')
                            ->send();
                    }),
            ]);
    }
}
