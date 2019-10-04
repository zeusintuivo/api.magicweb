<?php

namespace App\Models;

use App\Models\Traits\UserCanAuthenticate;
use App\Models\Traits\UserRelationships;

class User extends Model
{
    use UserCanAuthenticate, UserRelationships;

    protected $guarded = ['password', 'remember_token'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_sent_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

}
