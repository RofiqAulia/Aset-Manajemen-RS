<?php

namespace App\Filament\Resources\MaintenanceSchedules\Schemas;

use App\Models\MaintenanceSchedule;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MaintenanceScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('asset.name')
                    ->label('Nama Aset'),
                TextEntry::make('title')
                    ->label('Judul / Nama Pekerjaan'),
                TextEntry::make('frequency_days')
                    ->label('Interval (Hari)')
                    ->numeric(),
                TextEntry::make('last_performed_at')
                    ->label('Terakhir Dilakukan')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('next_due_date')
                    ->label('Jadwal Berikutnya')
                    ->date(),
                TextEntry::make('assignedTechnician.name')
                    ->label('Teknisi Penanggung Jawab')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),
                TextEntry::make('instructions')
                    ->label('Instruksi Pemeliharaan')
                    ->placeholder('-')
                    ->columnSpanFull(),
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
                    ->visible(fn (MaintenanceSchedule $record): bool => $record->trashed()),
            ]);
    }
}
