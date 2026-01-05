<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
          'company_id',
        'name',
        'price',
        'speed_limit',
        'framed_pool',
        'throttle_limit',
        'fup_limit',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'fup_limit' => 'integer',
    ];

      protected static function booted()
{
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new service',
            get_class($model),
            $model->id,   // service ID
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated service',
            get_class($model),
            $model->id,   // service ID
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted service',
            get_class($model),
            $model->id    // service ID
        );
    });
}
   public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}
}
