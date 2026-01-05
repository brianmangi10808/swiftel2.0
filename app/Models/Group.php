<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups'; // note: singular table name
    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'group_id');
    }

            protected static function booted()
{
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new group',
            get_class($model),
            $model->id,   // group ID
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated group',
            get_class($model),
            $model->id,   // group ID
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted group',
            get_class($model),
            $model->id    // group ID
        );
    });
}

   public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}
}
