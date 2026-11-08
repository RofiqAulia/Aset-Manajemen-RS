<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel(),
                DateTimePicker::make('email_verified_at')
                    ->label('Waktu Verifikasi Email'),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->required(),
                Select::make('role_id')
                    ->label('Peran / Hak Akses')
                    ->relationship('role', 'name'),
                Select::make('department_id')
                    ->label('Unit / Departemen')
                    ->relationship('department', 'name'),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->required(),
                DateTimePicker::make('last_login_at')
                    ->label('Terakhir Masuk'),
            ]);
    }
}
