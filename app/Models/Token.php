<?php

namespace App\Models;

use App\Exceptions\TokenExpiredException;
use Carbon\Carbon;

class Token extends Model
{
    protected $primaryKey = 'hash';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
