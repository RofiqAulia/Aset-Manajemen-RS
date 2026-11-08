<?php

namespace App\Filament\Resources\Locations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parent.name')
                    ->label('Induk Ruangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Kode Ruangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Ruangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis Lokasi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'building' => 'Gedung',
                        'floor' => 'Lantai',
                        'room' => 'Ruangan',
                        default => ucfirst((string) $state),
                    })
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Unit Penanggung Jawab')
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
            ])
            ->groups([
                Group::make('type')
                    ->label('Jenis Lokasi')
                    ->collapsible(),
                Group::make('department.name')
                    ->label('Unit Penanggung Jawab')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Jenis Lokasi')
                    ->options([
                        'building' => 'Gedung',
                        'floor' => 'Lantai',
                        'room' => 'Ruangan',
                    ]),
                SelectFilter::make('department')
                    ->label('Filter Unit Penanggung Jawab')
                    ->relationship('department', 'name'),
                SelectFilter::make('parent')
                    ->label('Filter Induk Ruangan')
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
