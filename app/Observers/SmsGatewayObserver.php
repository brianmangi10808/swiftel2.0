<?php

namespace App\Observers;

use App\Models\SmsGateway;

class SmsGatewayObserver
{
    /**
     * Handle the SmsGateway "created" event.
     */
    public function created(SmsGateway $smsGateway): void
    {
        //
    }
  public function saving(SmsGateway $gateway)
{
    // Ensure company_id is always set
    if (! $gateway->company_id) {
        throw new \Exception('Company ID must be set before saving a gateway.');
    }

    // If this gateway is being activated, deactivate all others in the same company
    if ($gateway->is_active) {
        SmsGateway::where('company_id', $gateway->company_id)
            ->where('id', '!=', $gateway->id)
            ->update(['is_active' => false]);
    }
}

    /**
     * Handle the SmsGateway "updated" event.
     */
    public function updated(SmsGateway $smsGateway): void
    {
        //
    }

    /**
     * Handle the SmsGateway "deleted" event.
     */
    public function deleted(SmsGateway $smsGateway): void
    {
        //
    }

    /**
     * Handle the SmsGateway "restored" event.
     */
    public function restored(SmsGateway $smsGateway): void
    {
        //
    }

    /**
     * Handle the SmsGateway "force deleted" event.
     */
    public function forceDeleted(SmsGateway $smsGateway): void
    {
        //
    }
}
