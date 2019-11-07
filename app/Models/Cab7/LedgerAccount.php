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

    public function skr04Account()
    {
        return $this->belongsTo(Skr04Account::class, 'skr04_id');
    }

    public function skr04RefAccount()
    {
        return $this->belongsTo(Skr04Account::class, 'skr04_ref_id');
    }

}
