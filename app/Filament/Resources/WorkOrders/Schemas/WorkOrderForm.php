<?php

namespace App\Filament\Resources\WorkOrders\Schemas;

use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderStatus;
use App\Support\DocumentNumberGenerator;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WorkOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('wo_number')
                    ->label('Nomor Work Order')
                    ->default(fn () => DocumentNumberGenerator::generateWorkOrderNumber())
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->helperText('Dihasilkan otomatis oleh sistem.'),
                Select::make('asset_id')
                    ->label('Pilihan Aset')
                    ->relationship('asset', 'name')
                    ->required(),
                Select::make('requester_id')
                    ->label('Pemohon')
                    ->relationship('requester', 'name')
                    ->required(),
                Select::make('technician_id')
                    ->label('Teknisi')
                    ->relationship('technician', 'name'),
                Select::make('priority')
                    ->label('Tingkat Prioritas')
                    ->options(WorkOrderPriority::class)
                    ->default('sedang')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(WorkOrderStatus::class)
                    ->default('baru')
                    ->required(),
                Textarea::make('description')
                    ->label('Deskripsi Masalah')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('action_taken')
                    ->label('Tindakan Perbaikan')
                    ->columnSpanFull(),
                TextInput::make('cost')
                    ->label('Biaya')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('Rp '),
                Textarea::make('completion_notes')
                    ->label('Catatan Penyelesaian')
                    ->columnSpanFull(),
                DateTimePicker::make('completed_at')
                    ->label('Waktu Selesai'),
            ]);
    }
}
