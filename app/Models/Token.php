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
        // Check time with Carbon
        $diff = Carbon::now()->diffInMinutes($this->created_at);
        $range = config('auth.passwords.users.expire');
        if ($diff > $range) {
            throw new TokenExpiredException();
        }
        return $this->belongsTo(User::class);
    }
}
