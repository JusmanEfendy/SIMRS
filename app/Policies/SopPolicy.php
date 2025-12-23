<?php

namespace App\Policies;

use App\Models\Sop;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SopPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Unit', 'Verifikator', 'Direksi', 'Direktorat']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sop $sop): bool
    {
        // Admin can view all SOPs
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Verifikator can view all SOPs
        if ($user->hasRole('Verifikator')) {
            return true;
        }
        
        // Direksi/Direktorat can view all SOPs where main unit OR collab units are in their directorate
        if ($user->hasAnyRole(['Direksi', 'Direktorat'])) {
            if (!$user->dir_id) {
                return false;
            }
            
            // Load the unit and collab units if not loaded
            $sop->load(['unit', 'collabUnits']);
            
            // Check if main unit is in user's directorate
            if ($sop->unit && (int) $sop->unit->dir_id === (int) $user->dir_id) {
                return true;
            }
            
            // Check if any collab unit is in user's directorate
            foreach ($sop->collabUnits as $collabUnit) {
                if ((int) $collabUnit->dir_id === (int) $user->dir_id) {
                    return true;
                }
            }
            
            return false;
        }
        
        // Unit can only view SOPs with status 'Aktif' that belong to their unit OR is a collab unit
        if ($user->hasRole('Unit')) {
            if ($sop->status !== 'Aktif' || !$user->id_unit) {
                return false;
            }
            
            // Check if user's unit is the main unit
            if ($sop->id_unit === $user->id_unit) {
                return true;
            }
            
            // Check if user's unit is a collab unit
            $sop->load('collabUnits');
            return $sop->collabUnits->contains('id_unit', $user->id_unit);
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Verifikator');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sop $sop): bool
    {
        return $user->hasRole('Verifikator');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sop $sop): bool
    {
        return $user->hasRole('Verifikator');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sop $sop): bool
    {
        return $user->hasRole('Verifikator');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sop $sop): bool
    {
        return $user->hasRole('Verifikator');
    }
}
