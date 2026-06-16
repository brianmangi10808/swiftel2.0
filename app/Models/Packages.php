<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class  Packages extends Model
{
    protected $connection = 'radius';
    protected $table = 'packages';
    
    protected $fillable = [
        'company_id',
        'name',
        'duration_sec',
        'speed_down',
        'speed_up',
        'price', 
        'is_active'

        
    ];
    

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_id');
    }
    

public function clients()
{
    return $this->hasMany(Clients::class, 'package_id');
}

   
  public function company()
    {
        return $this->belongsTo(Company::class);
    }
}