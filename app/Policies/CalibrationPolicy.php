<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Calibration;
use App\Models\User;

class CalibrationPolicy
{
    /**
     * Determine whether the user can view any calibrations.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->hasPermission('calibrations.manage')
            || $user->isAssetStaff()
            || $user->isManagement();
    }

    /**
     * Determine whether the user can view the calibration.
     */
    public function view(User $user, Calibration $calibration): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create calibrations.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('calibrations.manage');
    }

    /**
     * Determine whether the user can update the calibration.
     */
    public function update(User $user, Calibration $calibration): bool
    {
        return $user->isAdmin() || $user->hasPermission('calibrations.manage');
    }

    /**
     * Determine whether the user can delete the calibration.
     */
    public function delete(User $user, Calibration $calibration): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can bulk delete calibrations.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
