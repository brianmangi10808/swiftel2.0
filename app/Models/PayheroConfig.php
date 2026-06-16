<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayheroConfig extends Model
{
    protected $connection = 'radius';
    protected $table = 'payhero_configs';
    
 protected $primaryKey = 'gateway_id';
    
   
    public $incrementing = false;
    

    protected $keyType = 'int';
    
    protected $fillable = [
        'gateway_id',
        'company_id',
        'basic_auth',
        'api_username',
        'account_id',
        'api_password'
    ];
 
    

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_id');
    }
    
   
    
    // Helper to get auth headers for PayHero API
    public function getAuthHeaders(): array
    {
        return [
            'Authorization' => 'Basic ' . base64_encode($this->basic_auth),
            'Content-Type' => 'application/json',
        ];
    }
}