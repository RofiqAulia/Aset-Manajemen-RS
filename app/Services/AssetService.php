<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Support\AssetCodeGenerator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AssetService
{
    /**
     * Daftarkan aset baru ke dalam sistem.
     *
     * @param  array<string, mixed>  $data
     */
    public function createAsset(array $data, int $creatorId): Asset
    {
        return DB::transaction(function () use ($data, $creatorId): Asset {
            $data['asset_code'] = AssetCodeGenerator::generate();
            $data['created_by'] = $creatorId;
            $data['condition'] = $data['condition'] ?? AssetCondition::BAIK;
            $data['status'] = $data['status'] ?? AssetStatus::AKTIF;

            return Asset::create($data);
        });
    }

    /**
     * Perbarui data aset (kecuali lokasi yang wajib lewat mutasi).
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAsset(Asset $asset, array $data): Asset
    {
        // Penegakan aturan: Perubahan lokasi aset tidak boleh dilakukan langsung
        if (isset($data['location_id']) && $data['location_id'] !== $asset->location_id) {
            throw new InvalidArgumentException('Perubahan lokasi aset tidak dapat dilakukan langsung. Gunakan layanan mutasi aset (AssetMovementService).');
        }

        unset($data['location_id']);

        $asset->update($data);

        return $asset;
    }

    /**
     * Nonaktifkan aset dengan soft delete (tidak dihapus fisik dari basis data).
     */
    public function deactivateAsset(Asset $asset): bool
    {
        return DB::transaction(function () use ($asset): bool {
            $asset->update(['status' => AssetStatus::NON_AKTIF]);

            return (bool) $asset->delete();
        });
    }
}
