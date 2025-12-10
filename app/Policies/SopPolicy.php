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
        // return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sop $sop): bool
    {
        // return true;
        return $user->hasAnyRole(['Unit', 'Verifikator']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return true;
        return auth()->user()->hasRole('Unit');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sop $sop): bool
    {
        return auth()->user()->hasRole('Unit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sop $sop): bool
    {
        // return true;
         return auth()->user()->hasRole('Unit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sop $sop): bool
    {
        // return true;
         return auth()->user()->hasRole('Unit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sop $sop): bool
    {
        // return true;
         return auth()->user()->hasRole('Unit');
    }
}
