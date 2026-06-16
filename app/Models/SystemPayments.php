<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemPayments extends Model
{
    protected $connection = 'radius';
    protected $table = 'system_payments';
    
    protected $fillable = ['key', 'value', 'type'];
    
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        
        return match($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }
}