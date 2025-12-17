<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    // protected $primaryKey = 'id_unit';

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'dir_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sops()
    {
        // Foreign key is 'id_unit' on sops table, local key is 'id_unit' on units table
        return $this->hasMany(Sop::class, 'id_unit', 'id_unit');
    }
}
