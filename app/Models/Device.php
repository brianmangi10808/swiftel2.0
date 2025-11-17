<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Device extends Model
{

     use HasFactory;
      protected $fillable = [
        'name',
        'ip_address',
        'username',
        'api_port',
        'password',
        'location',
        'status',
    ];

       protected $hidden = [
        'password', 
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
