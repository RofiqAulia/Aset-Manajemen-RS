<?php

namespace App\Filament\Resources\WorkOrders\Tables;

use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderStatus;
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

class WorkOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('wo_number')
                    ->label('Nomor Work Order')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.name')
                    ->label('Nama Aset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('requester.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('technician.name')
                    ->label('Teknisi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('Tingkat Prioritas')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Pekerjaan')
                    ->badge()
                    ->sortable(),
                TextColumn::make('cost')
                    ->label('Biaya Perbaikan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label('Waktu Selesai')
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
                TextColumn::make('deleted_at')
                    ->label('Waktu Dihapus')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('status')
                    ->label('Status Pekerjaan')
                    ->collapsible(),
                Group::make('priority')
                    ->label('Tingkat Prioritas')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->label('Filter Tingkat Prioritas')
                    ->options(WorkOrderPriority::class),
                SelectFilter::make('status')
                    ->label('Filter Status Pekerjaan')
                    ->options(WorkOrderStatus::class),
                SelectFilter::make('asset')
                    ->label('Filter Aset')
                    ->relationship('asset', 'name'),
                SelectFilter::make('requester')
                    ->label('Filter Pemohon')
                    ->relationship('requester', 'name'),
                SelectFilter::make('technician')
                    ->label('Filter Teknisi')
                    ->relationship('technician', 'name'),
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
