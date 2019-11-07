<?php

namespace App\Models\Cab7;

use Illuminate\Database\Eloquent\Model;

class LedgerAccountView extends Model
{
    protected $table = 'cab7_ledger_accounts_balance';
    protected $casts = [
        'balance' => 'float',
    ];

    public function skr04()
    {
        return $this->belongsTo(Skr04Account::class);
    }

    public function pid()
    {
        return $this->belongsTo(Skr04Account::class);
    }
}
