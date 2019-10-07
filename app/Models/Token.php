<?php

namespace App\Models;

use App\Exceptions\TokenExpiredException;
use Carbon\Carbon;

class Token extends Model
{
    protected $primaryKey = 'hash';
    protected $hidden = [];
    protected $casts = [
        'hash' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
