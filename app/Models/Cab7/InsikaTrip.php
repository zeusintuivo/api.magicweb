<?php

namespace App\Models\Cab7;

class InsikaTrip extends Model
{
    // Allow to assign taxameter trip ids
    public $incrementing = false;
    protected $guarded = [];
    protected $connection = 'mysql-cab7';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
