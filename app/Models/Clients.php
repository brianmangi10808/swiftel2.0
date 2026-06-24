<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class  Clients extends Model
{
    protected $connection = 'radius';
    protected $table = 'clients';
    
    protected $fillable = [
        'company_id',
        'package_id',
        'package_name',
        'phone',
        'ip',
        'mac',
        'password',
        'router',
        'amount', 
        'status',
        'expiring_date',
        'created_at',
        'payment_status',
        'checkout_request_id',
        'mpesa_receipt'

        
    ];
    

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_id');
    }
    
    // app/Models/Clients.php
public function package()
{
    return $this->belongsTo(Packages::class, 'package_id');
}

public function sessions()
{
    return $this->hasMany(Radacct::class, 'username', 'phone');
}
 public function payment()
    {
        return $this->hasMany(\App\Models\Payment::class, 'bill_ref_number', 'username');
    }

      public function messages()
    {
        return $this->hasMany(\App\Models\Messages::class, 'recipient', 'username');
    }

  public function company()
    {
        return $this->belongsTo(Company::class);
    }
}