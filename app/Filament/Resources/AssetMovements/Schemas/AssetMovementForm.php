<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetMovements\Schemas;

use App\Enums\AssetMovementStatus;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Location;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssetMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->label('Pilihan Aset')
                    ->relationship('asset', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        if ($asset = Asset::find($state)) {
                            $set('from_location_id', $asset->location_id);
                            $set('from_department_id', $asset->department_id);
                        }
                    }),
                Select::make('from_location_id')
                    ->label('Ruangan / Lokasi Asal')
                    ->relationship('fromLocation', 'name')
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Select::make('from_department_id')
                    ->label('Unit / Departemen Asal')
                    ->relationship('fromDepartment', 'name')
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Select::make('to_location_id')
                    ->label('Ruangan / Lokasi Tujuan')
                    ->relationship('toLocation', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->different('from_location_id')
                    ->validationMessages([
                        'different' => 'Ruangan tujuan mutasi tidak boleh sama dengan ruangan asal aset saat ini.',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        if ($loc = Location::find($state)) {
                            if ($loc->department_id) {
                                $set('to_department_id', $loc->department_id);
                            }
                        }
                    }),
                Select::make('to_department_id')
                    ->label('Unit / Departemen Tujuan')
                    ->relationship('toDepartment', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->label('Status Mutasi')
                    ->options(AssetMovementStatus::class)
                    ->default(AssetMovementStatus::MENUNGGU_PERSETUJUAN)
                    ->required(),
                DateTimePicker::make('moved_at')
                    ->label('Waktu Mutasi')
                    ->default(now())
                    ->required(),
                Select::make('moved_by')
                    ->label('Petugas Pengaju')
                    ->relationship('mover', 'name')
                    ->default(fn () => auth()->id() ?? 1)
                    ->required(),
                Select::make('approved_by')
                    ->label('Pejabat Penyetuju')
                    ->relationship('approver', 'name')
                    ->visible(fn (string $operation): bool => $operation !== 'create'),
                Textarea::make('reason')
                    ->label('Alasan Mutasi')
                    ->placeholder('Jelaskan kebutuhan perpindahan unit atau relokasi aset...')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('rejection_reason')
                    ->label('Alasan Penolakan')
                    ->placeholder('Alasan penolakan mutasi lokasi...')
                    ->visible(fn (?AssetMovement $record): bool => $record?->status === AssetMovementStatus::DITOLAK)
                    ->columnSpanFull(),
            ]);
    }
}
