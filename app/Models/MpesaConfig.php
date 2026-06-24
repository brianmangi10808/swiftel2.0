<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaConfig extends Model
{
    protected $connection = 'radius';
    protected $table = 'mpesa_configs';
    
    protected $fillable = [
        'gateway_id',
        'company_id',
        'short_code',
        'passkey',
        'consumer_key',
        'consumer_secret'
        
    ];
    

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_id');
    }
    
   
}