<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->label('Induk Kategori / Ruangan')
                    ->relationship('parent', 'name'),
                TextInput::make('code')
                    ->label('Kode')
                    ->required(),
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('type')
                    ->label('Jenis / Tipe')
                    ->required(),
                Select::make('department_id')
                    ->label('Unit / Departemen')
                    ->relationship('department', 'name'),
            ]);
    }
}
