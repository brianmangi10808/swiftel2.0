<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{
    
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'mobile_number',
        'sector_id',
         'company_id',


    ];
    protected $casts = [
        'created_at'=>'datetime'
    ];
protected static function booted()
{
    static::created(function ($model) {
        log_activity(
            'created',
            'Created a new lead',
            get_class($model),
            $model->id,
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated a lead',
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
            'Deleted a lead',
            get_class($model),
            $model->id
        );
    });
}

       public function sector()
    {
        return $this->belongsTo(\App\Models\Sector::class, 'sector_id');
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\Service::class);
    }

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }

    public function premise()
    {
        return $this->belongsTo(\App\Models\Premise::class);
    }

    public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}

}
