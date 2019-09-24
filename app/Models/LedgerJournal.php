<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerJournal extends Model
{
    protected $connection = 'mysql-cab7';
    protected $table = 'ledger_journal';
    protected $guarded = [];
}
