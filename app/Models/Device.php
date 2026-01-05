<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Device extends Model
{

     use HasFactory;
      protected $fillable = [
         'company_id',
        'name',
        'ip_address',
        'username',
        'api_port',
        'password',
        'location',
        'status',
    ];

       protected $hidden = [
        'password', 
    ];

    protected $casts = [
        'status' => 'string',
    ];

           protected static function booted()
{
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new device',
            get_class($model),
            $model->id,   // device ID
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated device',
            get_class($model),
            $model->id,   
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted device',
            get_class($model),
            $model->id    
        );
    });
}
   public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}
}
