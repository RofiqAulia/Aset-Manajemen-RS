<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('contact_person')
                    ->label('Kontak Person (PIC)'),
                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel(),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->label('Email address')
                    ->email(),
                Textarea::make('address')
                    ->label('Alamat Lengkap')
                    ->columnSpanFull(),
                TextInput::make('type')
                    ->label('Jenis / Tipe')
                    ->required()
                    ->default('supplier'),
            ]);
    }
}
