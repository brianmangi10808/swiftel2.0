<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'template',
        'active',
    ];

          protected static function booted()
{
        static::creating(function ($smstemplate) {
        if (! $smstemplate->company_id && $smstemplate->customer) {
            $smstemplate->company_id = $smstemplate->customer->company_id;
        }
    });
    
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new smstemplate',
            get_class($model),
            $model->id,   // smstemplate ID
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated smstemplate',
            get_class($model),
            $model->id,   // smstemplate ID
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted smstemplate',
            get_class($model),
            $model->id    // smstemplate ID
        );
    });
}
public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}

}
