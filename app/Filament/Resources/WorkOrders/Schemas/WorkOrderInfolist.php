<?php

namespace App\Filament\Resources\WorkOrders\Schemas;

use App\Models\WorkOrder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WorkOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('wo_number')
                    ->label('Nomor Work Order'),
                TextEntry::make('asset.name')
                    ->label('Nama Aset'),
                TextEntry::make('requester.name')
                    ->label('Pemohon'),
                TextEntry::make('technician.name')
                    ->label('Teknisi')

                    ->placeholder('-'),
                TextEntry::make('priority')
                    ->label('Tingkat Prioritas')
                    ->badge(),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                TextEntry::make('description')
                    ->label('Deskripsi Masalah')
                    ->columnSpanFull(),
                TextEntry::make('action_taken')
                    ->label('Tindakan Perbaikan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('cost')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id'),
                TextEntry::make('completion_notes')
                    ->label('Catatan Penyelesaian')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('completed_at')
                    ->label('Waktu Selesai')
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
                TextEntry::make('deleted_at')
                    ->label('Waktu Dihapus')
                    ->dateTime()
                    ->visible(fn (WorkOrder $record): bool => $record->trashed()),
            ]);
    }
}
