<?php

namespace App\Models;

use App\Exceptions\TokenExpiredException;

class EmailAuthentication extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
