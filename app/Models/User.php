<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use App\Notifications\ResetPasswordNotification;


class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasFactory, Notifiable, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'is_super_admin',
        'email_verified_at',
        'otp',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }
 public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    /**
     * Determine if user can access Filament panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Super admins always have access
        if ($this->is_super_admin) {
            return true;
        }

        // Regular users need verified email AND at least one role
        return $this->hasVerifiedEmail() && $this->roles()->exists();
    }

    /**
     * Check if user can perform an action on a model
     */
    public function canDo(string $action, string $model): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        // Check using Spatie's permission system
        $permissionName = "{$action} {$model}";
        return $this->hasPermissionTo($permissionName);
    }

    /**
     * Relationships
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tickets()
    {
        return $this->hasMany(Tickets::class);
    }

    /**
     * Activity logging
     */
    protected static function booted()
    {
        static::created(function ($model) {
            log_activity(
                'created',
                'Created new user',
                get_class($model),
                $model->id,
                $model->toArray()
            );
        });

        static::updated(function ($model) {
            log_activity(
                'updated',
                'Updated user',
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
                'Deleted user',
                get_class($model),
                $model->id
            );
        });
    }
}