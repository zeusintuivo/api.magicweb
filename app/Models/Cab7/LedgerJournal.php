<?php

namespace App\Models\Cab7;

use App\Models\Model;
use App\Models\User;

class LedgerJournal extends Model
{
    protected $table = 'cab7_ledger_journal';
    protected $guarded = ['created_at', 'updated_at', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ledgerAccounts()
    {
        return $this->hasMany(LedgerAccount::class, 'journal_id');
    }
}
