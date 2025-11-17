<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SmsProvider extends Model
{
    protected $fillable = [
        'name',
        'provider_type',
        'configuration',
        'is_active',
        'is_default',
        'description',
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Boot method to handle default provider logic
     */
    protected static function boot()
    {
        parent::boot();

        // When a provider is set as default, unset others
        static::saving(function ($provider) {
            if ($provider->is_default) {
                static::where('id', '!=', $provider->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);

                self::clearCache();
            }
        });

        // Clear cache when provider is created, updated, or deleted
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }

    /**
     * Get the default SMS provider
     */
    public static function getDefault(): ?self
    {
        return Cache::remember('sms_provider_default', 3600, function () {
            return self::where('is_active', true)
                ->where('is_default', true)
                ->first();
        });
    }

    /**
     * Get all active providers
     */
    public static function getActive()
    {
        return Cache::remember('sms_providers_active', 3600, function () {
            return self::where('is_active', true)->get();
        });
    }

    /**
     * Get provider by type
     */
    public static function getByType(string $type): ?self
    {
        return self::where('provider_type', $type)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Clear all provider cache
     */
    public static function clearCache(): void
    {
        Cache::forget('sms_provider_default');
        Cache::forget('sms_providers_active');
    }

    /**
     * Get available provider types
     */
    public static function getAvailableTypes(): array
    {
        return [
            'simflix' => 'Simflix',
            'africastalking' => "Africa's Talking",
            'twilio' => 'Twilio',
            'suftech' => 'Suftech',
        ];
    }
}
