<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Collab extends Pivot
{
    protected $table = 'collabs';

    public $incrementing = false;

    protected $fillable = [
        'id_sop',
        'id_unit',
    ];

    public function sop()
    {
        return $this->belongsTo(Sop::class, 'id_sop', 'id_sop');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }
}
