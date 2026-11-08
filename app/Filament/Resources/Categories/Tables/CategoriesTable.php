<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parent.name')
                    ->label('Induk Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Kode Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_medical')
                    ->label('Kategori Medis')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('requires_calibration')
                    ->label('Wajib Kalibrasi')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('default_maintenance_interval_days')
                    ->label('Interval Pemeliharaan (Hari)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('default_calibration_interval_days')
                    ->label('Interval Kalibrasi (Hari)')
                    ->numeric()
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
            ])
            ->groups([
                Group::make('is_medical')
                    ->label('Kategori Medis')
                    ->collapsible(),
                Group::make('parent.name')
                    ->label('Induk Kategori')
                    ->collapsible(),
            ])
            ->filters([
                TernaryFilter::make('is_medical')
                    ->label('Filter Kategori Medis'),
                TernaryFilter::make('requires_calibration')
                    ->label('Filter Wajib Kalibrasi'),
                SelectFilter::make('parent')
                    ->label('Filter Induk Kategori')
                    ->relationship('parent', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
