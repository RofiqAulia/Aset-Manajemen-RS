<?php

namespace App\Filament\Resources\Vendors\Schemas;

use App\Models\Vendor;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama'),
                TextEntry::make('contact_person')
                    ->label('Kontak Person (PIC)')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label('Nomor Telepon')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Alamat Email')

                    ->placeholder('-'),
                TextEntry::make('address')
                    ->label('Alamat Lengkap')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('type')
                    ->label('Jenis / Tipe'),
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
                    ->visible(fn (Vendor $record): bool => $record->trashed()),
            ]);
    }
}
