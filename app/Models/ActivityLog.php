<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
          'company_id',
        'user_id',
        'action',
        'model',
        'model_id',
        'url',
        'ip_address',
        'description',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}
    public function getRelatedModel()
{
    if ($this->model && $this->model_id) {
        return $this->model::with(['sector', 'service', 'group', 'premise'])->find($this->model_id);
    }
    return null;
}


    public function getCustomerNameAttribute()
{
    if (!$this->model_id) return null;

    $customer = \App\Models\Customer::find($this->model_id);

    if (!$customer) return null;

    return $customer->firstname . ' ' . $customer->lastname;
}

public function getModelNameAttribute()
{
    // No model or ID? skip.
    if (!$this->model || !$this->model_id) {
        return null;
    }

    // Dynamically resolve model class
    $class = $this->model;

    if (!class_exists($class)) {
        return null;
    }

    $record = $class::find($this->model_id);

    if (!$record) {
        return null;
    }

    // Map for supported models → name field
    if ($record instanceof \App\Models\Customer) {
        return $record->firstname . ' ' . $record->lastname;
    }

    if ($record instanceof \App\Models\Leads) {
        return $record->firstname . ' ' . $record->lastname;
    }
    if ($record instanceof \App\Models\Device) {
        return $record->name ;
    }
      if ($record instanceof \App\Models\Group) {
        return $record->name ;
    }
      if ($record instanceof \App\Models\Premise) {
        return $record->name ;
    }
      if ($record instanceof \App\Models\Sector) {
        return $record->name ;
    }
      if ($record instanceof \App\Models\Service) {
        return $record->name ;
    }
     if ($record instanceof \App\Models\SmsGateway) {
        return $record->name ;
    }
    if ($record instanceof \App\Models\SmsTemplate) {
        return $record->type ;
    }
    if ($record instanceof \App\Models\User) {
        return $record->name ;
    }
    if ($record instanceof \App\Models\Payment) {
        return trim($record->first_name . ' ' . $record->last_name);
    }

    if ($record instanceof \App\Models\Tickets) {
        return 'Ticket #' . $record->id;
    }
    if ($record instanceof \App\Models\Customer) {
    return $record->firstname . ' ' . $record->lastname;
}


    // Default fallback
    return $record->id;
}

}
