<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = ['password', 'remember_token'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_sent_at' => 'datetime',
    ];
}
