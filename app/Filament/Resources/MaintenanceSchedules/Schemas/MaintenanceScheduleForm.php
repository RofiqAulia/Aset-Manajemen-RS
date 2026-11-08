<?php

namespace App\Filament\Resources\MaintenanceSchedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MaintenanceScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->label('Pilihan Aset')
                    ->relationship('asset', 'name')
                    ->required(),
                TextInput::make('title')
                    ->label('Judul / Nama Pekerjaan')
                    ->required(),
                TextInput::make('frequency_days')
                    ->label('Interval (Hari)')
                    ->required()
                    ->numeric()
                    ->default(30),
                DatePicker::make('last_performed_at')
                    ->label('Terakhir Dilakukan'),
                DatePicker::make('next_due_date')
                    ->label('Jadwal Berikutnya')
                    ->required(),
                Select::make('assigned_technician_id')
                    ->label('Teknisi Penanggung Jawab')
                    ->relationship('assignedTechnician', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->required(),
                Textarea::make('instructions')
                    ->label('Instruksi Pemeliharaan')
                    ->columnSpanFull(),
            ]);
    }
}
