<?php

namespace App\Policies;

use App\Models\Story;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Writer');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Story $story): bool
    {
        return $user->hasRole('Writer');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // bikin kondisi agar button create hanya muncul untuk role itu sendiri
        // return auth()->user()->hasRole('Writer');
        return $user->hasRole('Writer');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Story $story): bool
    {
        // bikin kondisi agar button edit hanya muncul untuk role itu sendiri dan hanya bisa mengedit story yang dia buat
        // return auth()->user()->hasRole('Writer') && $story->author_id === $user()->id;
        // return auth()->user()->hasRole('Writer') && $story->author_id === $user->id;
        return $user->hasRole('Writer') && $story->author_id === $user->id && in_array($story->status, ['rework', 'waiting for review']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Story $story): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Story $story): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Story $story): bool
    {
        return true;
    }
}
