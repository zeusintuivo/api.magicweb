<?php

namespace App\Models\Traits;

use App\Models\Token;

trait UserRelationships
{
    public function token()
    {
        return $this->hasOne(Token::class);
    }
}
