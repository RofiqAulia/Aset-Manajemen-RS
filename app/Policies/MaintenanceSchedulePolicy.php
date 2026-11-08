<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MaintenanceSchedule;
use App\Models\User;

class MaintenanceSchedulePolicy
{
    /**
     * Determine whether the user can view any maintenance schedules.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->hasPermission('maintenance.manage')
            || $user->isAssetStaff()
            || $user->isManagement();
    }

    /**
     * Determine whether the user can view the maintenance schedule.
     */
    public function view(User $user, MaintenanceSchedule $schedule): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create maintenance schedules.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('maintenance.manage');
    }

    /**
     * Determine whether the user can update the maintenance schedule.
     */
    public function update(User $user, MaintenanceSchedule $schedule): bool
    {
        return $user->isAdmin() || $user->hasPermission('maintenance.manage');
    }

    /**
     * Determine whether the user can delete the maintenance schedule.
     */
    public function delete(User $user, MaintenanceSchedule $schedule): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can bulk delete maintenance schedules.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
