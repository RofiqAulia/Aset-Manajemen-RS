<?php

namespace App\Filament\Resources\StockOpnames\Tables;

use App\Enums\StockOpnameResult;
use App\Models\Asset;
use App\Models\StockOpname;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class StockOpnamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opname_number')
                    ->label('Nomor Stock Opname')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Judul Kegiatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Lokasi Ruangan')
                    ->placeholder('Seluruh Rumah Sakit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Unit / Departemen')
                    ->placeholder('Seluruh Rumah Sakit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),
                TextColumn::make('auditor.name')
                    ->label('Auditor / Ketua Tim')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
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
                    })
                    ->sortable(),
                TextColumn::make('total_expected')
                    ->label('Total Terdaftar')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('total_scanned')
                    ->label('Total Terverifikasi')
                    ->numeric()
                    ->badge()
                    ->color(fn (StockOpname $record): string => $record->total_scanned >= $record->total_expected && $record->total_expected > 0 ? 'success' : 'info')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('total_discrepancy')
                    ->label('Selisih')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('discrepant_items_summary')
                    ->label('Barang Ditemukan Selisih')
                    ->state(function (StockOpname $record): string {
                        $items = $record->items()
                            ->where('status', 'selisih')
                            ->with('asset')
                            ->get();

                        if ($items->isEmpty()) {
                            return $record->total_scanned > 0 ? 'Sesuai (Tidak Ada Selisih)' : '-';
                        }

                        return $items->map(function ($item) {
                            $name = $item->asset?->name ?? 'Aset';
                            $diff = $item->discrepancy_quantity > 0 ? "+{$item->discrepancy_quantity}" : "{$item->discrepancy_quantity}";
                            $note = $item->notes ? " [Ket: {$item->notes}]" : '';

                            return "{$name} ({$diff} unit){$note}";
                        })->implode(', ');
                    })
                    ->badge(fn (string $state): bool => in_array($state, ['Sesuai (Tidak Ada Selisih)', '-'], true))
                    ->color(fn (string $state): string => match ($state) {
                        'Sesuai (Tidak Ada Selisih)' => 'success',
                        '-' => 'gray',
                        default => 'danger',
                    })
                    ->wrap()
                    ->searchable(false)
                    ->sortable(false),
                TextColumn::make('total_variance_value')
                    ->label('Nilai Selisih (Rp)')
                    ->money('IDR', locale: 'id')
                    ->color(fn (float $state): string => $state != 0.0 ? 'danger' : 'gray')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Waktu Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Waktu Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Waktu Dihapus')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('status')
                    ->label('Status Pelaksanaan')
                    ->collapsible(),
                Group::make('department.name')
                    ->label('Unit / Departemen')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
                SelectFilter::make('location')
                    ->label('Filter Lokasi')
                    ->relationship('location', 'name'),
                SelectFilter::make('department')
                    ->label('Filter Unit')
                    ->relationship('department', 'name'),
                TrashedFilter::make()->label('Filter Data Terhapus'),
            ])
            ->recordActions([
                Action::make('audit_asset')
                    ->label('Cari & Cek Fisik Aset')
                    ->icon('heroicon-o-magnifying-glass-plus')
                    ->color('primary')
                    ->modalHeading('Pencarian Inventaris & Pencatatan Selisih Fisik')
                    ->modalDescription('Cari barang di data inventaris, tentukan hasil penghitungan fisik, centang jika terdapat selisih, dan berikan keterangan.')
                    ->modalSubmitActionLabel('Simpan Hasil Pemeriksaan')
                    ->modalWidth('xl')
                    ->form([
                        Select::make('asset_id')
                            ->label('Cari Barang di Data Inventaris')
                            ->options(function (StockOpname $record): array {
                                $query = Asset::query();
                                if ($record->location_id) {
                                    $query->where('location_id', $record->location_id);
                                }
                                if ($record->department_id) {
                                    $query->where('department_id', $record->department_id);
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
                    ->action(function (StockOpname $record, array $data): void {
                        $asset = Asset::findOrFail($data['asset_id']);
                        $expectedStock = (int) ($asset->stock ?? 1);
                        $actualStock = (int) $data['actual_stock'];
                        $diff = $actualStock - $expectedStock;
                        $unitCost = (float) ($asset->acquisition_cost ?? 0);
                        $diffValue = $diff * $unitCost;
                        $hasDiscrepancy = (bool) ($data['has_discrepancy'] ?? false) || ($diff !== 0);

                        $item = $record->items()->firstOrNew(['asset_id' => $asset->id]);
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

                        $record->recalculateMetrics();

                        Notification::make()
                            ->title($hasDiscrepancy ? 'Tercatat Selisih Barang' : 'Barang Diverifikasi Sesuai')
                            ->body("Aset [{$asset->name}] berhasil diperiksa. Selisih: {$diff} unit.".($data['notes'] ? " Keterangan: {$data['notes']}" : ''))
                            ->color($hasDiscrepancy ? 'warning' : 'success')
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
