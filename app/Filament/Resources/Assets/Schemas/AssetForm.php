<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Support\DocumentNumberGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('asset_code')
                    ->label('Kode Aset')
                    ->default(fn () => DocumentNumberGenerator::generateAssetCode())
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->helperText('Dihasilkan otomatis oleh sistem.'),
                TextInput::make('name')
                    ->label('Nama Aset')
                    ->required(),
                TextInput::make('stock')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->default(1)
                    ->required(),
                TextInput::make('safety_stock')
                    ->label('Batas Minimum (Safety Stock)')
                    ->numeric()
                    ->default(1)
                    ->required(),
                TextInput::make('brand')
                    ->label('Merek'),
                TextInput::make('model')
                    ->label('Model / Tipe'),
                TextInput::make('serial_number')
                    ->label('Nomor Seri'),
                DatePicker::make('acquisition_date')
                    ->label('Tanggal Perolehan'),
                TextInput::make('acquisition_cost')
                    ->label('Biaya Perolehan')
                    ->numeric()
                    ->prefix('Rp '),
                TextInput::make('funding_source')
                    ->label('Sumber Dana'),
                DatePicker::make('warranty_expiry_date')
                    ->label('Batas Garansi'),
                Select::make('condition')
                    ->label('Kondisi')
                    ->options(AssetCondition::class)
                    ->default('baik')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(AssetStatus::class)
                    ->default('aktif')
                    ->required(),
                Toggle::make('is_critical')
                    ->label('Aset Kritis')
                    ->required(),
                FileUpload::make('photo_url')
                    ->label('Foto / Gambar Aset')
                    ->image()
                    ->imageEditor()
                    ->directory('assets/photos')
                    ->disk('public')
                    ->visibility('public')
                    ->extraInputAttributes(['capture' => 'environment'])
                    ->maxSize(5120)
                    ->helperText('Dapat mengunggah berkas gambar (JPG, PNG, WebP) atau klik tombol Buka Kamera Langsung untuk mengambil foto secara instan.')
                    ->columnSpanFull()
                    ->hintAction(
                        Action::make('captureFromCamera')
                            ->label('Buka Kamera Langsung')
                            ->icon('heroicon-m-camera')
                            ->color('primary')
                            ->button()
                            ->modalHeading('Ambil Foto Langsung Melalui Kamera')
                            ->modalDescription('Arahkan kamera ke perangkat atau aset rumah sakit, lalu klik tombol Jepret / Ambil Foto.')
                            ->modalSubmitActionLabel('Gunakan Foto Ini')
                            ->modalCancelActionLabel('Batal')
                            ->modalWidth('xl')
                            ->form([
                                ViewField::make('captured_image')
                                    ->label('Kamera')
                                    ->view('filament.forms.components.camera-capture')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Silakan jepret foto terlebih dahulu menggunakan tombol kamera.',
                                    ]),
                            ])
                            ->action(function (array $data, Set $set): void {
                                $base64 = $data['captured_image'] ?? null;
                                if (! $base64 || ! is_string($base64)) {
                                    return;
                                }

                                if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                                    $imageData = substr($base64, strpos($base64, ',') + 1);
                                    $extension = strtolower($matches[1]);
                                    $extension = $extension === 'jpeg' ? 'jpg' : $extension;
                                    $decoded = base64_decode($imageData);

                                    if ($decoded !== false) {
                                        $filename = 'assets/photos/'.Str::random(40).'.'.$extension;
                                        Storage::disk('public')->put($filename, $decoded);
                                        $set('photo_url', $filename);

                                        Notification::make()
                                            ->title('Foto Berhasil Disimpan')
                                            ->body('Foto aset berhasil diambil dari kamera dan dipasangkan.')
                                            ->success()
                                            ->send();
                                    }
                                }
                            })
                    ),
                Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->label('Kategori Aset')
                    ->relationship('category', 'name')
                    ->required(),
                Select::make('location_id')
                    ->label('Ruangan / Lokasi')
                    ->relationship('location', 'name')
                    ->required(),
                Select::make('department_id')
                    ->label('Unit / Departemen')
                    ->relationship('department', 'name')
                    ->required(),
                Select::make('vendor_id')
                    ->label('Vendor Rekanan')
                    ->relationship('vendor', 'name'),
                Select::make('created_by')
                    ->label('Dibuat Oleh')
                    ->relationship('creator', 'name')
                    ->default(fn () => auth()->id() ?? 1)
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
