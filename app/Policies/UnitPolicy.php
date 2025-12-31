<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UnitPolicy
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
     */
    public function viewAny(User $user): bool
    {
        // Admin dan Direksi dapat melihat daftar unit
        return $user->hasRole('Admin') || $this->isDireksi($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Unit $unit): bool
    {
        // Admin bisa lihat semua
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Direksi hanya bisa lihat unit di bawah direktorat mereka
        if ($this->isDireksi($user)) {
            // Gunakan dir_id dari tabel users
            return $user->dir_id && $unit->dir_id == $user->dir_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya Admin yang bisa create
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Unit $unit): bool
    {
        // Hanya Admin yang bisa update
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Unit $unit): bool
    {
        // Hanya Admin yang bisa delete
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Unit $unit): bool
    {
        // Hanya Admin yang bisa restore
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Unit $unit): bool
    {
        // Hanya Admin yang bisa force delete
        return $user->hasRole('Admin');
    }
}
