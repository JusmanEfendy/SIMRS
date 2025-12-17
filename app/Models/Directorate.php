<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directorate extends Model
{
    /**
     * Get the user (head) of this directorate.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all units under this directorate.
     */
    public function units()
    {
        return $this->hasMany(Unit::class, 'dir_id');
    }
}
