<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
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
                Toggle::make('is_medical')
                    ->label('Kategori Medis')
                    ->required(),
                Toggle::make('requires_calibration')
                    ->label('Wajib Kalibrasi')
                    ->required(),
                TextInput::make('default_maintenance_interval_days')
                    ->label('Interval Pemeliharaan (Hari)')
                    ->numeric(),
                TextInput::make('default_calibration_interval_days')
                    ->label('Interval Kalibrasi (Hari)')
                    ->numeric(),
            ]);
    }
}
