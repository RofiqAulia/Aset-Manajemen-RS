<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssetMovementService
{
    /**
     * Ajukan atau eksekusi mutasi aset antar ruangan/departemen.
     *
     * @throws ValidationException
     */
    public function moveAsset(
        Asset $asset,
        int $toLocationId,
        int $toDepartmentId,
        int $movedById,
        string $reason
    ): AssetMovement {
        // Validasi: Lokasi tujuan tidak boleh sama dengan lokasi aset saat ini (Kasus 5)
        if ($asset->location_id === $toLocationId) {
            throw ValidationException::withMessages([
                'to_location_id' => 'Lokasi tujuan sama dengan lokasi aset saat ini. Pilih ruangan lain untuk memindahkan aset.',
            ]);
        }

        $targetDepartment = Department::findOrFail($toDepartmentId);
        $requiresApproval = $targetDepartment->requires_movement_approval;
        $status = $requiresApproval ? 'menunggu_persetujuan' : 'disetujui';

        return DB::transaction(function () use ($asset, $toLocationId, $toDepartmentId, $movedById, $reason, $status, $requiresApproval): AssetMovement {
            $movement = AssetMovement::create([
                'asset_id' => $asset->id,
                'from_location_id' => $asset->location_id,
                'to_location_id' => $toLocationId,
                'from_department_id' => $asset->department_id,
                'to_department_id' => $toDepartmentId,
                'moved_by' => $movedById,
                'status' => $status,
                'moved_at' => now(),
                'reason' => $reason,
            ]);

            // Jika tidak butuh persetujuan, perbarui lokasi dan departemen aset langsung
            if (! $requiresApproval) {
                $asset->update([
                    'location_id' => $toLocationId,
                    'department_id' => $toDepartmentId,
                ]);
            }

            return $movement;
        });
    }

    /**
     * Setujui mutasi aset yang menunggu persetujuan kepala unit tujuan.
     */
    public function approveMovement(AssetMovement $movement, int $approverId): AssetMovement
    {
        return DB::transaction(function () use ($movement, $approverId): AssetMovement {
            $movement->update([
                'status' => 'disetujui',
                'approved_by' => $approverId,
            ]);

            $movement->asset->update([
                'location_id' => $movement->to_location_id,
                'department_id' => $movement->to_department_id,
            ]);

            return $movement;
        });
    }

    /**
     * Tolak mutasi aset yang diajukan.
     */
    public function rejectMovement(AssetMovement $movement, int $approverId, string $rejectionReason): AssetMovement
    {
        $movement->update([
            'status' => 'ditolak',
            'approved_by' => $approverId,
            'rejection_reason' => $rejectionReason,
        ]);

        return $movement;
    }
}
