<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('parent.name')
                    ->label('Induk')

                    ->placeholder('-'),
                TextEntry::make('code')
                    ->label('Kode'),
                TextEntry::make('name')
                    ->label('Nama'),
                IconEntry::make('is_medical')
                    ->label('Kategori Medis')
                    ->boolean(),
                IconEntry::make('requires_calibration')
                    ->label('Wajib Kalibrasi')
                    ->boolean(),
                TextEntry::make('default_maintenance_interval_days')
                    ->label('Interval Pemeliharaan (Hari)')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('default_calibration_interval_days')
                    ->label('Interval Kalibrasi (Hari)')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Waktu Dibuat')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Waktu Diperbarui')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
