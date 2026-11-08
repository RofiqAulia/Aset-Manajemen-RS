<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Asset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('asset_code')
                    ->label('Kode Aset'),
                TextEntry::make('name')
                    ->label('Nama Aset'),
                TextEntry::make('stock')
                    ->label('Jumlah Stok')
                    ->badge()
                    ->color(fn (Asset $record): string => $record->stock <= $record->safety_stock ? 'danger' : 'success'),
                TextEntry::make('safety_stock')
                    ->label('Batas Minimum (Safety Stock)')
                    ->badge()
                    ->color('gray'),
                TextEntry::make('brand')
                    ->label('Merek')
                    ->placeholder('-'),
                TextEntry::make('model')
                    ->label('Model / Tipe')
                    ->placeholder('-'),
                TextEntry::make('serial_number')
                    ->label('Nomor Seri')
                    ->placeholder('-'),
                TextEntry::make('acquisition_date')
                    ->label('Tanggal Perolehan')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('acquisition_cost')
                    ->label('Biaya Perolehan')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-'),
                TextEntry::make('funding_source')
                    ->label('Sumber Dana')
                    ->placeholder('-'),
                TextEntry::make('warranty_expiry_date')
                    ->label('Batas Garansi')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('condition')
                    ->label('Kondisi')
                    ->badge(),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                IconEntry::make('is_critical')
                    ->label('Aset Kritis')
                    ->boolean(),
                ImageEntry::make('photo_url')
                    ->label('Foto / Gambar Aset')
                    ->disk('public')
                    ->placeholder('Belum Ada Foto')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->label('Catatan Tambahan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('category.name')
                    ->label('Kategori Aset'),
                TextEntry::make('location.name')
                    ->label('Ruangan / Lokasi'),
                TextEntry::make('department.name')
                    ->label('Unit / Departemen'),
                TextEntry::make('vendor.name')
                    ->label('Vendor Rekanan')

                    ->placeholder('-'),
                TextEntry::make('creator.name')
                    ->label('Dibuat Oleh')
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
                    ->visible(fn (Asset $record): bool => $record->trashed()),
            ]);
    }
}
