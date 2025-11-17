<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
     use SoftDeletes;
 use HasFactory;

   
    protected $fillable = [
        'username',
        'password',
        'status',
        'enable',
        'sector_id',
        'premise_id',
        'service_id',       
        'allow_mac',
        'simultaneous_use',
        'Calling_Station_Id',
        'group_id',
        'credit',
        'firstname',
        'lastname',
        'mobile_number',
        'email',
        'expiry_date',
        'comment',
        'attribute',
        'created_at'
        
    ];

       protected $auditInclude = [
        'username',
        'status',
        'enable',
        'service_id',
        'expiry_date',
        'credit',
        'firstname',
        'lastname',
        'mobile_number',
        'email',
    ];

    protected $casts = [
        'enable' => 'boolean',
        'expiry_date' => 'datetime',
    ];
        protected $dates = ['deleted_at'];


   
    public function service()
    {
        return $this->belongsTo(\App\Models\Service::class, 'service_id');
    }

    public function sector()
    {
        return $this->belongsTo(\App\Models\Sector::class, 'sector_id');
    }

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class, 'group_id');
    }

    public function premise()
    {
        return $this->belongsTo(\App\Models\Premise::class, 'premise_id');
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'bill_ref_number', 'username');
    }

      public function radacct()
    {
        return $this->hasMany(\App\Models\Radacct::class, 'username', 'username');
    }
       public function authlog()
    {
        return $this->hasMany(\App\Models\AuthLog::class, 'username', 'username');
    }

      public function messages()
    {
        return $this->hasMany(\App\Models\Messages::class, 'recipient', 'username');
    }
    public function tickets()
{
    return $this->hasMany(Tickets::class);
}

}



