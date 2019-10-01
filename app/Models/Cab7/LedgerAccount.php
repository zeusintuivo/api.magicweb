<?php

namespace App\Models\Cab7;

use App\Models\AccountChart;
use App\Models\LedgerJournal;
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

    public function accountChart()
    {
        return $this->belongsTo(AccountChart::class);
    }

    public function ledgerJournal()
    {
        return $this->belongsTo(LedgerJournal::class);
    }

}
