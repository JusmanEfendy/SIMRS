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
        return $user->hasAnyRole(['Unit', 'Verifikator']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sop $sop): bool
    {
        // Verifikator can view all SOPs
        if ($user->hasRole('Verifikator')) {
            return true;
        }
        
        // Unit can only view SOPs with status 'Aktif' that belong to their unit
        if ($user->hasRole('Unit')) {
            return $sop->status === 'Aktif' 
                && $user->id_unit 
                && $sop->id_unit === $user->id_unit;
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
