<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifyMikrotikBackend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $customerId) {}

    public function handle(): void
    {
        try {
            Http::timeout(10)->post(env('MIKROTIK_BACKEND_URL') . '/api/customer-updated', [
                'customer_id' => $this->customerId,
            ]);
            
            Log::info('Backend notified for customer: ' . $this->customerId);
        } catch (\Exception $e) {
            Log::error('Failed to notify backend: ' . $e->getMessage());
            throw $e;
        }
    }
}