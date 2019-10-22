<?php

namespace App\Models\Cab7;

use App\Models\Model;

class PersonalAccount extends Model
{
    protected $table = 'cab7_personal_accounts';
    public $incrementing = false;

    public function skr04Account()
    {
        return $this->belongsTo(Skr04Account::class);
    }
}
