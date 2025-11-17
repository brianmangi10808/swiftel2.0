<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SmsSettings extends Model
{
    protected $fillable = ['key', 'value', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("sms_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'is_active' => true]
        );
        
        Cache::forget("sms_setting_{$key}");
    }

    /**
     * Check if a setting is enabled
     */
    public static function isEnabled(string $key): bool
    {
        return Cache::remember("sms_setting_enabled_{$key}", 3600, function () use ($key) {
            $setting = self::where('key', $key)->first();
            return $setting && $setting->is_active && $setting->value == '1';
        });
    }

    /**
     * Get multiple settings at once
     */
    public static function getMultiple(array $keys): array
    {
        $settings = self::whereIn('key', $keys)->get();
        
        $result = [];
        foreach ($keys as $key) {
            $setting = $settings->firstWhere('key', $key);
            $result[$key] = $setting ? $setting->value : null;
        }
        
        return $result;
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }
}