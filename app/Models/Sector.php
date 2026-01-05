<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
          'company_id',
        'name',
        'description',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
   public function tickets()
    {
        return $this->hasMany(Tickets::class);
    }
       public function leads()
    {
        return $this->hasMany(Leads::class);
    }
          protected static function booted()
{
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new sector',
            get_class($model),
            $model->id,   // sector ID
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated sector',
            get_class($model),
            $model->id,   // sector ID
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted sector',
            get_class($model),
            $model->id    // sector ID
        );
    });
}
   public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}
}
