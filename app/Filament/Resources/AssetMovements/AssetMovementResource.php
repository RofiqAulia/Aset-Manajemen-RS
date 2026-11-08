<?php

namespace App\Filament\Resources\AssetMovements;

use App\Filament\Resources\AssetMovements\Pages\CreateAssetMovement;
use App\Filament\Resources\AssetMovements\Pages\EditAssetMovement;
use App\Filament\Resources\AssetMovements\Pages\ListAssetMovements;
use App\Filament\Resources\AssetMovements\Pages\ViewAssetMovement;
use App\Filament\Resources\AssetMovements\Schemas\AssetMovementForm;
use App\Filament\Resources\AssetMovements\Schemas\AssetMovementInfolist;
use App\Filament\Resources\AssetMovements\Tables\AssetMovementsTable;
use App\Models\AssetMovement;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AssetMovementResource extends Resource
{
    protected static ?string $model = AssetMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Aset';

    protected static ?string $navigationLabel = 'Mutasi Lokasi';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Mutasi Aset';

    protected static ?string $pluralModelLabel = 'Mutasi Lokasi';

    protected static ?string $recordTitleAttribute = 'movement_code';

    /**
     * Mutasi bisa diakses oleh: admin, petugas_aset, kepala_unit (approve), manajemen (view).
     */
    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->isAdmin()
            || $user->isAssetStaff()
            || $user->isUnitHead()
            || $user->isManagement();
    }

    public static function form(Schema $schema): Schema
    {
        return AssetMovementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetMovementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetMovementsTable::configure($table);
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
            'index' => ListAssetMovements::route('/'),
            'create' => CreateAssetMovement::route('/create'),
            'view' => ViewAssetMovement::route('/{record}'),
            'edit' => EditAssetMovement::route('/{record}/edit'),
        ];
    }
}
