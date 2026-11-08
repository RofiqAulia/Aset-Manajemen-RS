<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetMovements\Pages;

use App\Enums\AssetMovementStatus;
use App\Filament\Resources\AssetMovements\AssetMovementResource;
use App\Models\Asset;
use App\Models\AssetMovement;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateAssetMovement extends CreateRecord
{
    protected static string $resource = AssetMovementResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $asset = Asset::findOrFail($data['asset_id']);

        if ((int) $asset->location_id === (int) $data['to_location_id']) {
            throw ValidationException::withMessages([
                'to_location_id' => 'Lokasi tujuan sama dengan lokasi aset saat ini. Pilih ruangan lain untuk memindahkan aset.',
            ]);
        }

        return DB::transaction(function () use ($data, $asset): Model {
            $movement = AssetMovement::create($data);

            if ($movement->status === AssetMovementStatus::DISETUJUI) {
                $asset->update([
                    'location_id' => $movement->to_location_id,
                    'department_id' => $movement->to_department_id,
                ]);
            }

            return $movement;
        });
    }
}
