<?php

namespace App\Model;

use App\Models\AccountChart;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LedgerAccount extends Model
{
    protected $connection = 'mysql-cab7';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accountChart()
    {
        return $this->belongsTo(AccountChart::class);
    }

}
