<?php

namespace App\Models;

class InsikaTrip extends Model
{
    // Allow to assign taxameter trip ids
    public $incrementing = false;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
