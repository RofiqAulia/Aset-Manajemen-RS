<?php

namespace App\Filament\Resources\StockOpnames\Schemas;

use App\Support\DocumentNumberGenerator;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockOpnameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('opname_number')
                    ->label('Nomor Stock Opname')
                    ->default(fn () => DocumentNumberGenerator::generateStockOpnameNumber())
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->helperText('Dihasilkan otomatis oleh sistem.'),
                TextInput::make('title')
                    ->label('Judul / Nama Pekerjaan')
                    ->required(),
                Select::make('location_id')
                    ->label('Ruangan / Lokasi (Kosongkan Untuk Seluruh RS)')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('department_id')
                    ->label('Unit / Departemen (Kosongkan Untuk Seluruh RS)')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->default(now())
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai'),
                Select::make('auditor_id')
                    ->label('Auditor / Ketua Tim')
                    ->relationship('auditor', 'name')
                    ->default(fn () => auth()->id() ?? 1)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->label('Status Pelaksanaan')
                    ->options([
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('berjalan')
                    ->required(),
                TextInput::make('total_expected')
                    ->label('Total Terdaftar')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->default(0)
                    ->visible(fn (string $operation): bool => $operation !== 'create'),
                TextInput::make('total_scanned')
                    ->label('Total Terverifikasi')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->default(0)
                    ->visible(fn (string $operation): bool => $operation !== 'create'),
                TextInput::make('total_discrepancy')
                    ->label('Total Selisih')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->default(0)
                    ->visible(fn (string $operation): bool => $operation !== 'create'),
                Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
            ]);
    }
}
