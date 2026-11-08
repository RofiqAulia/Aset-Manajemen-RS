<?php

namespace App\Filament\Resources\Calibrations\Schemas;

use App\Enums\CalibrationResult;
use App\Support\DocumentNumberGenerator;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CalibrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->label('Pilihan Aset')
                    ->relationship('asset', 'name')
                    ->required(),
                Select::make('vendor_id')
                    ->label('Vendor Rekanan')
                    ->relationship('vendor', 'name'),
                DatePicker::make('calibration_date')
                    ->label('Tanggal Kalibrasi')
                    ->required(),
                DatePicker::make('expiry_date')
                    ->label('Masa Berlaku')
                    ->required(),
                TextInput::make('certificate_number')
                    ->label('Nomor Sertifikat')
                    ->default(fn () => DocumentNumberGenerator::generateCalibrationCertificateNumber())
                    ->helperText('Dihasilkan otomatis atau isi nomor sertifikat eksternal.'),
                Select::make('result')
                    ->label('Hasil Kalibrasi')
                    ->options(CalibrationResult::class)
                    ->default('lulus')
                    ->required(),
                TextInput::make('cost')
                    ->label('Biaya')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('Rp '),
                TextInput::make('tested_by')
                    ->label('Petugas Penguji'),
                Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
            ]);
    }
}
