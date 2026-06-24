<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankConfig extends Model
{
    protected $connection = 'radius';
    protected $table = 'bank_configs';

    protected $primaryKey = 'gateway_id';
    public $incrementing = false;
    protected $keyType = 'int';
    
    protected $fillable = [
        'gateway_id',
        'company_id',
        'bank_name',
        'bank_paybill',
        'bank_account_number',
        'channel_id'
        
    ];
    
    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_id');
    }
}