<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Unit')
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Unit / Instalasi')
                    ->required(),
                Select::make('head_user_id')
                    ->label('Kepala Unit')
                    ->relationship('headUser', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('requires_movement_approval')
                    ->label('Wajib Persetujuan Mutasi')
                    ->required(),
            ]);
    }
}
