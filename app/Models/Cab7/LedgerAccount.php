<?php

namespace App\Models\Cab7;

use App\Models\Model;
use App\Models\User;

class LedgerAccount extends Model
{
    protected $table = 'cab7_ledger_accounts';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skr04()
    {
        return $this->belongsTo(Skr04Account::class);
    }

    public function journal()
    {
        return $this->belongsTo(LedgerJournal::class);
    }

}
