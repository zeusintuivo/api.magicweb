<?php

namespace App\Models\Cab7;

use App\Models\Model;

class LedgerAccount extends Model
{
    protected $table = 'cab7_ledger_accounts';
    protected $guarded = ['created_at', 'updated_at', 'deleted_at'];

    public function journal()
    {
        return $this->belongsTo(LedgerJournal::class);
    }

    public function skr04()
    {
        return $this->belongsTo(Skr04Account::class);
    }

    public function skr04_ref()
    {
        return $this->belongsTo(Skr04Account::class);
    }

}
