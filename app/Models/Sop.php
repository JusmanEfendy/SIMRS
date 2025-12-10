<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sop extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_sop = 'SOP' . now()->format('YmdHis');
        });
    }
}
