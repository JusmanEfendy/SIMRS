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

    public function sops()
    {
        return $this->hasMany(Sop::class, 'unit_id');
    }

}
