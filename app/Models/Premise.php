<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Premise extends Model
{
    use HasFactory;

    protected $fillable = [
          'company_id',
        'name',
        'description',
        'status',
        'available',
    ];

    protected $casts = [
        'available' => 'boolean',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'premise_id');
    }

           protected static function booted()
{
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new premise',
            get_class($model),
            $model->id,   // premise ID
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated premise',
            get_class($model),
            $model->id,   // premise ID
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted premise',
            get_class($model),
            $model->id    // premise ID
        );
    });
}
   public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}

}
