<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsGateway extends Model
{
    protected $table = 'sms_gateways';

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'credentials',
        'is_active',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_active' => 'boolean',
    ];

          protected static function booted()
{
    
static::saving(function ($gateway) {
    if ($gateway->is_active) {
        static::where('company_id', $gateway->company_id)
            ->where('id', '!=', $gateway->id)
            ->update(['is_active' => false]);
    }
});

    static::created(function ($model) {
        log_activity(
            'created',
            'Created new smsgateway',
            get_class($model),
            $model->id,   // smsgateway ID
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated smsgateway',
            get_class($model),
            $model->id,   // smsgateway ID
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted smsgateway',
            get_class($model),
            $model->id    // smsgateway ID
        );
    });
}
public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}
}
