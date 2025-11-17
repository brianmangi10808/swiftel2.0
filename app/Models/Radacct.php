<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radacct extends Model
{
    use HasFactory;

    protected $table = 'radacct';
    

  
    // Set the primary key
    protected $primaryKey = 'radacctid';

    // Indicate that the primary key is auto-incrementing
    public $incrementing = true;

    // Specify the primary key's data type
    protected $keyType = 'int';

    // Cast datetime fields
    protected $casts = [
        'acctstarttime' => 'datetime',
        'acctupdatetime' => 'datetime',
        'acctstoptime' => 'datetime',
    ];

    // Add any necessary relationships here
    protected $fillable = [
      
        'nasipaddress',
        'acctstarttime',
        'acctupdatetime',
        'acctstoptime',
        'acctsessiontime',
        'acctinputoctets',
        'acctoutputoctets',
        'acctterminatecause',
        'framedipaddress',
    ];
}
