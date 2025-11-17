<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'speed_limit',
        'framed_pool',
        'throttle_limit',
        'fup_limit',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'fup_limit' => 'integer',
    ];


}
