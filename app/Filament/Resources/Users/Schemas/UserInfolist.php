<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama'),
                TextEntry::make('email')
                    ->label('Alamat Email'),
                TextEntry::make('phone')
                    ->label('Nomor Telepon')
                    ->placeholder('-'),
                TextEntry::make('email_verified_at')
                    ->label('Waktu Verifikasi Email')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('role.name')
                    ->label('Peran / Hak Akses')

                    ->placeholder('-'),
                TextEntry::make('department.name')
                    ->label('Unit / Departemen')

                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),
                TextEntry::make('last_login_at')
                    ->label('Terakhir Masuk')
                    ->dateTime()
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
