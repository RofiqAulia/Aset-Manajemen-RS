<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LocationInfolist
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
                TextEntry::make('type')
                    ->label('Jenis / Tipe'),
                TextEntry::make('department.name')
                    ->label('Unit / Departemen')

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
