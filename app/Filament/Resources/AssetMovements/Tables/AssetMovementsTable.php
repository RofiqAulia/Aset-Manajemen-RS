<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetMovements\Tables;

use App\Enums\AssetMovementStatus;
use App\Models\AssetMovement;
use App\Services\AssetMovementService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class AssetMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('moved_at')
                    ->label('Waktu Mutasi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('asset.asset_code')
                    ->label('Kode Aset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.name')
                    ->label('Nama Aset')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('fromLocation.name')
                    ->label('Ruangan Asal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('toLocation.name')
                    ->label('Ruangan Tujuan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fromDepartment.name')
                    ->label('Unit Asal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('toDepartment.name')
                    ->label('Unit Tujuan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mover.name')
                    ->label('Petugas Pengaju')
                    ->sortable(),
                TextColumn::make('approver.name')
                    ->label('Pejabat Penyetuju')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Mutasi')
                    ->badge()
                    ->sortable(),
                TextColumn::make('reason')
                    ->label('Alasan Mutasi')
                    ->limit(35)
                    ->tooltip(fn (AssetMovement $record): ?string => $record->reason)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rejection_reason')
                    ->label('Alasan Penolakan')
                    ->limit(35)
                    ->placeholder('-')
                    ->tooltip(fn (AssetMovement $record): ?string => $record->rejection_reason)
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
            ])
            ->groups([
                Group::make('status')
                    ->label('Status Mutasi')
                    ->collapsible(),
                Group::make('fromDepartment.name')
                    ->label('Unit Asal')
                    ->collapsible(),
                Group::make('toDepartment.name')
                    ->label('Unit Tujuan')
                    ->collapsible(),
                Group::make('fromLocation.name')
                    ->label('Ruangan Asal')
                    ->collapsible(),
                Group::make('toLocation.name')
                    ->label('Ruangan Tujuan')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status Mutasi')
                    ->options(AssetMovementStatus::class),
                SelectFilter::make('asset')
                    ->label('Filter Aset')
                    ->relationship('asset', 'name'),
                SelectFilter::make('fromLocation')
                    ->label('Filter Ruangan Asal')
                    ->relationship('fromLocation', 'name'),
                SelectFilter::make('toLocation')
                    ->label('Filter Ruangan Tujuan')
                    ->relationship('toLocation', 'name'),
                SelectFilter::make('fromDepartment')
                    ->label('Filter Unit Asal')
                    ->relationship('fromDepartment', 'name'),
                SelectFilter::make('toDepartment')
                    ->label('Filter Unit Tujuan')
                    ->relationship('toDepartment', 'name'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (AssetMovement $record): bool => $record->status === AssetMovementStatus::MENUNGGU_PERSETUJUAN)
                    ->requiresConfirmation()
                    ->modalHeading('Persetujuan Mutasi Lokasi Aset')
                    ->modalDescription('Apakah Anda yakin menyetujui mutasi ini? Lokasi dan departemen aset akan langsung diperbarui ke tujuan.')
                    ->action(function (AssetMovement $record): void {
                        app(AssetMovementService::class)->approveMovement($record, auth()->id() ?? 1);
                        Notification::make()
                            ->title('Mutasi Lokasi Aset Berhasil Disetujui')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (AssetMovement $record): bool => $record->status === AssetMovementStatus::MENUNGGU_PERSETUJUAN)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan Mutasi')
                            ->required()
                            ->placeholder('Tuliskan alasan penolakan mutasi aset...'),
                    ])
                    ->action(function (AssetMovement $record, array $data): void {
                        app(AssetMovementService::class)->rejectMovement($record, auth()->id() ?? 1, $data['rejection_reason']);
                        Notification::make()
                            ->title('Mutasi Lokasi Aset Telah Ditolak')
                            ->warning()
                            ->send();
                    }),
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
