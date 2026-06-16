<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
class PaymentGateway extends Model
{
    use SoftDeletes;
    
    protected $connection = 'radius';
    protected $table = 'payment_gateways';
    
    protected $fillable = [
        'company_id',
        'gateway_type',
        'name',
        'is_active',
        'is_default'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function companyPayments()
    {
        return $this->hasMany(Payment::class, 'company_id', 'company_id');
    }
    

    
    // ✅ Or use accessor with eager loading
    public function scopeWithConfig($query)
    {
        return $query->with(['mpesaConfig', 'payheroConfig', 'bankConfig']);
    }
    
    public function mpesaConfig()
    {
        return $this->hasOne(MpesaConfig::class, 'gateway_id');
    }
    
    public function payheroConfig()
    {
        return $this->hasOne(PayheroConfig::class, 'gateway_id');
    }
    
    public function bankConfig()
    {
        return $this->hasOne(BankConfig::class, 'gateway_id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
    
    protected static function booted()
    {
        static::saving(function ($gateway) {
            if ($gateway->is_default) {
                static::where('company_id', $gateway->company_id)
                    ->where('id', '!=', $gateway->id)
                    ->update(['is_default' => false]);
            }
        });

         static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('company_id', auth()->user()->company_id);
            }
        });
    }
}