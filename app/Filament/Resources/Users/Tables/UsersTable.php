<?php

namespace App\Filament\Resources\Users\Tables;

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

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role.name')
                    ->label('Peran / Hak Akses')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Unit / Departemen')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('last_login_at')
                    ->label('Terakhir Masuk')
                    ->dateTime()
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
                Group::make('role.name')
                    ->label('Peran / Hak Akses')
                    ->collapsible(),
                Group::make('department.name')
                    ->label('Unit / Departemen')
                    ->collapsible(),
                Group::make('is_active')
                    ->label('Status Aktif')
                    ->collapsible(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Filter Status Aktif Akun'),
                SelectFilter::make('role')
                    ->label('Filter Peran / Hak Akses')
                    ->relationship('role', 'name'),
                SelectFilter::make('department')
                    ->label('Filter Unit / Departemen')
                    ->relationship('department', 'name'),
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
