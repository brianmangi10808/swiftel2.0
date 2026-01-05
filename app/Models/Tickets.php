<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    use HasFactory;

    protected $table ='tickets';
    protected $fillable = [
        'company_id',
        'customer_id',
        'description',
        'status',
        'severity',
        'comment',
        'created_at',
        'resolution_notes',
        'resolved_at'
    ];


    protected static function booted()
{
      static::creating(function ($ticket) {
        if (! $ticket->company_id && $ticket->customer) {
            $ticket->company_id = $ticket->customer->company_id;
        }
    });
    static::created(function ($model) {
        log_activity(
            'created',
            'Created new ticket',
            get_class($model),
            $model->customer_id,
            $model->toArray()
        );
    });

    static::updated(function ($model) {
        log_activity(
            'updated',
            'Updated ticket',
            get_class($model),
            $model->customer_id,
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    });

    static::deleted(function ($model) {
        log_activity(
            'deleted',
            'Deleted ticket',
            get_class($model),
            $model->customer_id
        );
    });
}

    public function sector()
    {
        return $this->belongsTo(\App\Models\Sector::class, 'sector_id');
    }

        public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function customer()
{
    return $this->belongsTo(Customer::class);
}

public function sendTicketSms(string $type): bool
{
    $template = \App\Models\SmsTemplate::where('type', $type)
        ->where('active', true)
        ->first();

    if (! $template) {
        return false;
    }

    $message = $this->processTicketTemplate($template->template);

    return app(\App\Services\SmsGatewayService::class)
        ->sendSms($this->customer->mobile_number, $message);
}

protected function processTicketTemplate(string $template): string
{
    $customer = $this->customer;

    $replacements = [
        '{firstname}'          => $customer->firstname ?? '',
        '{lastname}'           => $customer->lastname ?? '',
        '{username}'           => $customer->username ?? '',
        '{email}'              => $customer->email ?? '',
        '{mobile_number}'      => $customer->mobile_number ?? '',
        '{service}'            => $customer->service->name ?? '',
        '{sector}'             => $customer->sector->name ?? '',
        '{group}'              => $customer->group->name ?? '',
        '{expiry_date}'        => optional($customer->expiry_date)->format('Y-m-d'),
        '{credit}'             => $customer->credit ?? '',

        // Ticket-specific placeholders
        '{ticket_id}'          => $this->id,
        '{ticket_description}' => $this->description ?? '',
        '{ticket_severity}'    => $this->severity ?? '',
        '{ticket_status}'      => $this->status ?? '',
        '{resolution_notes}'   => $this->resolution_notes ?? '',
        '{resolved_at}'        => optional($this->resolved_at)->format('Y-m-d H:i'),
    ];

    return str_replace(
        array_keys($replacements),
        array_values($replacements),
        $template
    );
}
public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}


}
