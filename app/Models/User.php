<?php

namespace App\Models;

use App\Models\Traits\UserCanAuthenticate;
use App\Models\Traits\UserRelationships;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes, UserCanAuthenticate, UserRelationships;

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['last_email_at', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that aren't mass assignable
     * @var array
     */
    protected $guarded = ['id', 'password', 'created_at', 'updated_at', 'deleted_at'];

    protected $hidden = ['password'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

}
