<?php

namespace App\Filament\Resources\Departments\Tables;

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

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Unit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Unit / Instalasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('headUser.name')
                    ->label('Kepala Unit')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('requires_movement_approval')
                    ->label('Persetujuan Mutasi')
                    ->boolean()
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
                Group::make('requires_movement_approval')
                    ->label('Persetujuan Mutasi')
                    ->collapsible(),
            ])
            ->filters([
                TernaryFilter::make('requires_movement_approval')
                    ->label('Filter Persetujuan Mutasi'),
                SelectFilter::make('headUser')
                    ->label('Filter Kepala Unit')
                    ->relationship('headUser', 'name'),
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
