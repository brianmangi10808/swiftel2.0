<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class AuthLog extends Model
{

     use HasFactory;
      protected $fillable = [
        'username',
        'mac',
        'nas',
        'action',
        'status',
        'ip_address',
        'session_id',
        'reason',
        'created_at'
    ];

    //    protected $hidden = [
    //     'password', 
    // ];

    // protected $casts = [
    //     'status' => 'string',
    // ];
}
