<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpirySnapshot extends Model
{
     protected $fillable = [
            'company_id',
        'snapshot_date',
        'currently_expired',
        'renewed_today',
        'new_expiries_today',
        'active_users',
    ];
}
