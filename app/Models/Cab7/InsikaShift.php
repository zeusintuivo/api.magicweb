<?php

namespace App\Models\Cab7;

use App\Models\Model;
use App\Models\User;

class InsikaShift extends Model
{
    // Allow to assign taxameter trip ids
    public $incrementing = false;
    protected $table = 'cab7_insika_shifts';
    protected $guarded = ['created_at', 'updated_at', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
