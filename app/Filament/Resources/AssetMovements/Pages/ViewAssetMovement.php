<?php

namespace App\Filament\Resources\AssetMovements\Pages;

use App\Filament\Resources\AssetMovements\AssetMovementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAssetMovement extends ViewRecord
{
    protected static string $resource = AssetMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
