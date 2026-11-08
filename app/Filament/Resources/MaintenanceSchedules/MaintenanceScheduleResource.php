<?php

namespace App\Filament\Resources\MaintenanceSchedules;

use App\Filament\Resources\MaintenanceSchedules\Pages\CreateMaintenanceSchedule;
use App\Filament\Resources\MaintenanceSchedules\Pages\EditMaintenanceSchedule;
use App\Filament\Resources\MaintenanceSchedules\Pages\ListMaintenanceSchedules;
use App\Filament\Resources\MaintenanceSchedules\Pages\ViewMaintenanceSchedule;
use App\Filament\Resources\MaintenanceSchedules\Schemas\MaintenanceScheduleForm;
use App\Filament\Resources\MaintenanceSchedules\Schemas\MaintenanceScheduleInfolist;
use App\Filament\Resources\MaintenanceSchedules\Tables\MaintenanceSchedulesTable;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class MaintenanceScheduleResource extends Resource
{
    protected static ?string $model = MaintenanceSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static UnitEnum|string|null $navigationGroup = 'Layanan & Pemeliharaan';

    protected static ?string $navigationLabel = 'Jadwal Pemeliharaan';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Jadwal Pemeliharaan';

    protected static ?string $pluralModelLabel = 'Jadwal Pemeliharaan';

    protected static ?string $recordTitleAttribute = 'title';

    /**
     * Jadwal pemeliharaan diakses oleh: admin, teknisi, petugas_aset, manajemen (view).
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
        return MaintenanceScheduleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MaintenanceScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceSchedulesTable::configure($table);
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
            'index' => ListMaintenanceSchedules::route('/'),
            'create' => CreateMaintenanceSchedule::route('/create'),
            'view' => ViewMaintenanceSchedule::route('/{record}'),
            'edit' => EditMaintenanceSchedule::route('/{record}/edit'),
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
