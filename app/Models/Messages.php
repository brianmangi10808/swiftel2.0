<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Messages extends Model
{

     use HasFactory;
      protected $table = 'messages';
      protected $fillable = [
        'recipient',
        'message_body',
        'channel',
        'created_at',
     
    ];

    //    protected $hidden = [
    //     'password', 
    // ];

    // protected $casts = [
    //     'status' => 'string',
    // ];
}
