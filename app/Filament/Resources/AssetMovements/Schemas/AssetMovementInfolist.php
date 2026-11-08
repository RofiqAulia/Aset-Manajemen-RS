<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetMovements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AssetMovementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('asset.asset_code')
                    ->label('Kode Aset'),
                TextEntry::make('asset.name')
                    ->label('Nama Aset'),
                TextEntry::make('fromLocation.name')
                    ->label('Ruangan / Lokasi Asal'),
                TextEntry::make('toLocation.name')
                    ->label('Ruangan / Lokasi Tujuan'),
                TextEntry::make('fromDepartment.name')
                    ->label('Unit / Departemen Asal'),
                TextEntry::make('toDepartment.name')
                    ->label('Unit / Departemen Tujuan'),
                TextEntry::make('mover.name')
                    ->label('Petugas Pengaju Mutasi'),
                TextEntry::make('approver.name')
                    ->label('Pejabat Penyetuju')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label('Status Mutasi')
                    ->badge(),
                TextEntry::make('moved_at')
                    ->label('Waktu Pelaksanaan Mutasi')
                    ->dateTime('d M Y H:i'),
                TextEntry::make('reason')
                    ->label('Alasan Mutasi')
                    ->columnSpanFull(),
                TextEntry::make('rejection_reason')
                    ->label('Alasan Penolakan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Waktu Pengajuan')
                    ->dateTime('d M Y H:i'),
                TextEntry::make('updated_at')
                    ->label('Pembaruan Terakhir')
                    ->dateTime('d M Y H:i'),
            ]);
    }
}
