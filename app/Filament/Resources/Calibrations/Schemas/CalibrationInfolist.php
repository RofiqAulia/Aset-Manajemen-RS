<?php

namespace App\Filament\Resources\Calibrations\Schemas;

use App\Models\Calibration;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CalibrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('asset.name')
                    ->label('Nama Aset'),
                TextEntry::make('vendor.name')
                    ->label('Vendor Rekanan')

                    ->placeholder('-'),
                TextEntry::make('calibration_date')
                    ->label('Tanggal Kalibrasi')
                    ->date(),
                TextEntry::make('expiry_date')
                    ->label('Masa Berlaku')
                    ->date(),
                TextEntry::make('certificate_number')
                    ->label('Nomor Sertifikat')
                    ->placeholder('-'),
                TextEntry::make('result')
                    ->label('Hasil Kalibrasi')
                    ->badge(),
                TextEntry::make('cost')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id'),
                TextEntry::make('tested_by')
                    ->label('Petugas Penguji')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label('Catatan Tambahan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Waktu Dibuat')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Waktu Diperbarui')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->label('Waktu Dihapus')
                    ->dateTime()
                    ->visible(fn (Calibration $record): bool => $record->trashed()),
            ]);
    }
}
