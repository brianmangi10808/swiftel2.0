<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
         'company_id',
        'transaction_type',
        'trans_id',
        'trans_time',
        'trans_amount',
        'business_short_code',
        'bill_ref_number',
        'invoice_number',
        'org_account_balance',
        'third_party_trans_id',
        'msisdn',
        'first_name',
        'middle_name',
        'last_name',
    ];


    protected static function booted()
{
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new Payments',
            get_class($model),
            $model->bill_ref_number,
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated Paments',
            get_class($model),
            $model->bill_ref_number,
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted Payments',
            get_class($model),
            $model->bill_ref_number
        );
    });
}

public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}
}
