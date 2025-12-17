<?php

namespace App\Policies;

use App\Models\SopHistory;
use App\Models\User;

class SopHistoryPolicy
{
    /**
     * Check if user has Direksi-related roles.
     */
    private function isDireksi(User $user): bool
    {
        return $user->hasRole('Direksi') || $user->hasRole('Direktorat');
    }

    /**
     * Determine whether the user can view any models.
     * Only Direksi can access the SOP History menu.
     */
    public function viewAny(User $user): bool
    {
        return $this->isDireksi($user);
    }

    /**
     * Determine whether the user can view the model.
     * Direksi can only view history from units under their directorate.
     */
    public function view(User $user, SopHistory $sopHistory): bool
    {
        if (!$this->isDireksi($user)) {
            return false;
        }

        // Check if history belongs to a unit under user's directorate
        $userDirId = $user->dir_id;
        if (!$userDirId) {
            return false;
        }

        return $sopHistory->sop?->unit?->dir_id == $userDirId;
    }

    /**
     * Determine whether the user can create models.
     * History is created automatically, not manually.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     * History should not be editable.
     */
    public function update(User $user, SopHistory $sopHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * History should not be deletable.
     */
    public function delete(User $user, SopHistory $sopHistory): bool
    {
        return false;
    }
}
