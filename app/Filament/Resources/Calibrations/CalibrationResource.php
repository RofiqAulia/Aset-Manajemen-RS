<?php

namespace App\Filament\Resources\Calibrations;

use App\Filament\Resources\Calibrations\Pages\CreateCalibration;
use App\Filament\Resources\Calibrations\Pages\EditCalibration;
use App\Filament\Resources\Calibrations\Pages\ListCalibrations;
use App\Filament\Resources\Calibrations\Pages\ViewCalibration;
use App\Filament\Resources\Calibrations\Schemas\CalibrationForm;
use App\Filament\Resources\Calibrations\Schemas\CalibrationInfolist;
use App\Filament\Resources\Calibrations\Tables\CalibrationsTable;
use App\Models\Calibration;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CalibrationResource extends Resource
{
    protected static ?string $model = Calibration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static UnitEnum|string|null $navigationGroup = 'Layanan & Pemeliharaan';

    protected static ?string $navigationLabel = 'Kalibrasi Alat Medis';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Kalibrasi';

    protected static ?string $pluralModelLabel = 'Kalibrasi Alat Medis';

    protected static ?string $recordTitleAttribute = 'certificate_number';

    /**
     * Kalibrasi diakses oleh: admin, teknisi, petugas_aset, manajemen (view).
     */
    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->isAdmin()
            || $user->isTechnician()
            || $user->isAssetStaff()
            || $user->isManagement();
    }

    public static function form(Schema $schema): Schema
    {
        return CalibrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CalibrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalibrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalibrations::route('/'),
            'create' => CreateCalibration::route('/create'),
            'view' => ViewCalibration::route('/{record}'),
            'edit' => EditCalibration::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
