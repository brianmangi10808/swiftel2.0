<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

use Illuminate\Notifications\Notifiable;
use App\Models\SmsTemplate;
use App\Services\SmsGatewayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use App\Models\SmsProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Customer extends Model
{
     use SoftDeletes;
 use HasFactory;

   
    protected $fillable = [
         'company_id',
        'username',
        'password',
        'status',
        'enable',
        'sector_id',
        'premise_id',
        'service_id',       
        'allow_mac',
        'simultaneous_use',
        'Calling_Station_Id',
        'group_id',
        'credit',
        'firstname',
        'lastname',
        'mobile_number',
        'email',
        'expiry_date',
        'comment',
        'attribute',
        'created_at'
        
    ];


    protected $casts = [
        'enable' => 'boolean',
        'expiry_date' => 'datetime',
    ];
        protected $dates = ['deleted_at'];


        protected static function booted()
{
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new customer',
            get_class($model),
            $model->id,   // customer ID
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated customer',
            get_class($model),
            $model->id,   // customer ID
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted customer',
            get_class($model),
            $model->id    // customer ID
        );
    });
}



public function sendSmsFromTemplate(string $type): bool
{
    $template = \App\Models\SmsTemplate::where('type', $type)
        ->where('active', true)
        ->first();

    if (! $template) {
        return false;
    }

    $message = $this->processSmsTemplate($template->template);

    return app(\App\Services\SmsGatewayService::class)
        ->sendSms($this->mobile_number, $message);
}
protected function processSmsTemplate(string $template): string
{
    $replacements = [
        '{firstname}'    => $this->firstname ?? '',
        '{lastname}'     => $this->lastname ?? '',
        '{username}'     => $this->username ?? '',
        '{expiry_date}'  => optional($this->expiry_date)->format('Y-m-d'),
        '{service}'      => $this->service->name ?? '',
        '{sector}'       => $this->sector->name ?? '',
        '{group}'        => $this->group->name ?? '',
        '{credit}'       => $this->credit ?? '',
        '{email}'        => $this->email ?? '',
        '{mobile_number}' => $this->mobile_number ?? '',
    ];

    return str_replace(
        array_keys($replacements),
        array_values($replacements),
        $template
    );
}

  public function sendPruneNoticeSms(): bool
    {
        return $this->sendSmsFromTemplate('prune_notice');
    }


public function sendCustomSms(string $message): bool
{
    if (! $this->mobile_number) {
        return false;
    }

    
    $gatewayService = App::make(SmsGatewayService::class);

    return $gatewayService->sendSms($this->mobile_number, $message);
}



   
    public function service()
    {
        return $this->belongsTo(\App\Models\Service::class, 'service_id');
    }

    public function sector()
    {
        return $this->belongsTo(\App\Models\Sector::class, 'sector_id');
    }

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class, 'group_id');
    }

    public function premise()
    {
        return $this->belongsTo(\App\Models\Premise::class, 'premise_id');
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'bill_ref_number', 'username');
    }

      public function radacct()
    {
        return $this->hasMany(\App\Models\Radacct::class, 'username', 'username');
    }
       public function authlog()
    {
        return $this->hasMany(\App\Models\AuthLog::class, 'username', 'username');
    }

      public function messages()
    {
        return $this->hasMany(\App\Models\Messages::class, 'recipient', 'username');
    }
    public function tickets()
{
    return $this->hasMany(Tickets::class);
}

public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}


public function extensions()
{
    return $this->hasMany(Extension::class);
}

}



