<?php

namespace App\Filament\Resources\Vendors\Tables;

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

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Vendor / Perusahaan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_person')
                    ->label('Kontak Person (PIC)')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis Usaha')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'supplier' => 'Penyedia (Supplier)',
                        'servis' => 'Bengkel / Servis',
                        'kalibrasi' => 'Lembaga Kalibrasi',
                        default => ucfirst((string) $state),
                    })
                    ->color('info')
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
                Group::make('type')
                    ->label('Jenis Usaha')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Jenis Usaha')
                    ->options([
                        'supplier' => 'Penyedia (Supplier)',
                        'servis' => 'Bengkel / Servis',
                        'kalibrasi' => 'Lembaga Kalibrasi',
                    ]),
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
