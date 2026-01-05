<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
          'company_id',
    'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
           'is_super_admin' => 'boolean',

        ];
    }
       public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

public function permissions()
{
    return $this->belongsToMany(Permission::class, 'user_permissions');
}

public function canDo(string $model, string $action): bool
{
    // Super Admin bypass
    if ($this->is_super_admin || $this->role === 'super_admin') {
        return true;
    }

    return $this->permissions()
        ->where('model', $model)
        ->where('action', $action)
        ->exists();
}


       public function tickets()
    {
        return $this->hasMany(Tickets::class);
    }

          protected static function booted()
{
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new users',
            get_class($model),
            $model->id,   // users ID
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated users',
            get_class($model),
            $model->id,   // users ID
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted users',
            get_class($model),
            $model->id    // users ID
        );
    });
}
}
