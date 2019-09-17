<?php

namespace App\Models;

class Trip extends Model
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
