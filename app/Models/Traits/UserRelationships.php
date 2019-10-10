<?php

namespace App\Models\Traits;

use App\Models\EmailAuthentication;

trait UserRelationships
{
    public function token()
    {
        return $this->hasOne(EmailAuthentication::class);
    }
}
