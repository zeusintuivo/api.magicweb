<?php

namespace App\Models\Cab7;

use App\Models\Model;

class Skr04Account extends Model
{
    protected $table = 'cab7_skr04_accounts';
    public $incrementing = false;

    public function personalAccounts()
    {
        return $this->hasMany(PersonalAccount::class);
    }
}
