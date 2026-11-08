<?php

namespace App\Filament\Resources\Calibrations\Tables;

use App\Enums\CalibrationResult;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class CalibrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.name')
                    ->label('Nama Alat Medis')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vendor.name')
                    ->label('Lembaga Kalibrasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('calibration_date')
                    ->label('Tanggal Kalibrasi')
                    ->date()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->label('Masa Berlaku')
                    ->date()
                    ->sortable(),
                TextColumn::make('certificate_number')
                    ->label('Nomor Sertifikat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('result')
                    ->label('Hasil Kalibrasi')
                    ->badge()
                    ->sortable(),
                TextColumn::make('cost')
                    ->label('Biaya Kalibrasi')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('tested_by')
                    ->label('Petugas Penguji')
                    ->searchable()
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
                Group::make('result')
                    ->label('Hasil Kalibrasi')
                    ->collapsible(),
                Group::make('vendor.name')
                    ->label('Lembaga Kalibrasi')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('result')
                    ->label('Filter Hasil Kalibrasi')
                    ->options(CalibrationResult::class),
                SelectFilter::make('asset')
                    ->label('Filter Alat Medis')
                    ->relationship('asset', 'name'),
                SelectFilter::make('vendor')
                    ->label('Filter Lembaga Kalibrasi')
                    ->relationship('vendor', 'name'),
                TrashedFilter::make()->label('Filter Data Terhapus'),
            ])
            ->recordActions([
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
