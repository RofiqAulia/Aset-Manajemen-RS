<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Services\AssetExcelService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => asset('assets/img/logo-rs-ummi-png.png')),
                TextColumn::make('asset_code')
                    ->label('Kode Aset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Aset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->badge()
                    ->color(fn (Asset $record): string => $record->stock <= $record->safety_stock ? 'danger' : 'success')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('safety_stock')
                    ->label('Batas Minimum')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('brand')
                    ->label('Merek')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->label('Model / Tipe')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serial_number')
                    ->label('Nomor Seri')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('acquisition_date')
                    ->label('Tanggal Perolehan')
                    ->date()
                    ->sortable(),
                TextColumn::make('acquisition_cost')
                    ->label('Biaya Perolehan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('funding_source')
                    ->label('Sumber Dana')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('warranty_expiry_date')
                    ->label('Batas Garansi')
                    ->date()
                    ->sortable(),
                TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_critical')
                    ->label('Aset Kritis')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Ruangan / Lokasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Unit / Departemen')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vendor.name')
                    ->label('Vendor Rekanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Group::make('category.name')
                    ->label('Kategori Aset')
                    ->collapsible(),
                Group::make('location.name')
                    ->label('Ruangan / Lokasi')
                    ->collapsible(),
                Group::make('department.name')
                    ->label('Unit / Departemen')
                    ->collapsible(),
                Group::make('condition')
                    ->label('Kondisi')
                    ->collapsible(),
                Group::make('status')
                    ->label('Status Operasional')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('condition')
                    ->label('Filter Kondisi')
                    ->options(AssetCondition::class),
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(AssetStatus::class),
                SelectFilter::make('category')
                    ->label('Filter Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('location')
                    ->label('Filter Lokasi')
                    ->relationship('location', 'name'),
                SelectFilter::make('department')
                    ->label('Filter Unit')
                    ->relationship('department', 'name'),
                SelectFilter::make('vendor')
                    ->label('Filter Vendor')
                    ->relationship('vendor', 'name'),
                TernaryFilter::make('is_critical')
                    ->label('Filter Aset Kritis'),
                TrashedFilter::make()->label('Filter Data Terhapus'),
            ])
            ->recordActions([
                Action::make('print_qr')
                    ->label('Cetak QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->visible(fn () => auth()->user()?->isAdmin() || auth()->user()?->isAssetStaff())
                    ->url(fn (Asset $record): string => route('assets.print-qr', ['asset' => $record]))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->headerActions([
                Action::make('download_template')
                    ->label('Unduh Template .xlsx')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(fn () => app(AssetExcelService::class)->downloadTemplate()),

                Action::make('import_excel')
                    ->label('Impor Excel (.xlsx)')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->form([
                        FileUpload::make('file')
                            ->label('Pilih File Excel (.xlsx)')
                            ->helperText('Hanya menerima file spreadsheet Microsoft Excel berekstensi .xlsx')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->disk('local')
                            ->directory('temp-imports')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $filePath = Storage::disk('local')->path($data['file']);
                        if (! file_exists($filePath)) {
                            $filePath = storage_path('app/'.$data['file']);
                        }

                        $service = app(AssetExcelService::class);
                        $result = $service->import($filePath, auth()->id() ?? 1);

                        @unlink($filePath);

                        if ($result['failed'] === 0) {
                            Notification::make()
                                ->title('Impor Aset Berhasil')
                                ->body("Berhasil mengimpor {$result['success']} data inventaris aset.")
                                ->success()
                                ->send();
                        } else {
                            $errorMessages = implode("\n", array_slice($result['errors'], 0, 3));
                            Notification::make()
                                ->title("Impor Selesai ({$result['success']} Berhasil, {$result['failed']} Gagal)")
                                ->body($errorMessages)
                                ->warning()
                                ->persistent()
                                ->send();
                        }
                    }),

                Action::make('export_excel')
                    ->label('Ekspor Excel (.xlsx)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->with([
                            'category',
                            'location',
                            'department',
                            'vendor',
                        ])->get();

                        return app(AssetExcelService::class)->export($records);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print_qr_bulk')
                        ->label('Cetak Label QR Terpilih')
                        ->icon('heroicon-o-qr-code')
                        ->color('info')
                        ->visible(fn () => auth()->user()?->isAdmin() || auth()->user()?->isAssetStaff())
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->implode(',');

                            return redirect()->route('assets.print-qr-bulk', ['ids' => $ids]);
                        }),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
