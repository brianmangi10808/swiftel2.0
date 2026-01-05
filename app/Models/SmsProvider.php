<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsProvider extends Model
{
    protected $fillable = [
        'provider_name',
        'api_url',
        'api_key',
        'sender_id',
        'active',
    ];
}
