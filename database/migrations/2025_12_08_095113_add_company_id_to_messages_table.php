<?php

namespace App\Services;

use App\Models\Messages;
use Illuminate\Support\Facades\Auth;

class SmsLogger
{
    public static function log(
        string $recipient,
        string $message,
        string $channel,
        string $status = 'sent',
        ?int $companyId = null
    ) {
        // ✅ Auto-detect company if not passed explicitly
        $companyId ??= Auth::user()?->company_id;

        if (! $companyId) {
            throw new \Exception('Company context missing for SMS log.');
        }

        return Messages::create([
            'company_id'   => $companyId,
            'recipient'    => $recipient,
            'message_body' => $message,
            'channel'      => $channel,
            'status'       => $status,
            'scheduled_at' => now(),
        ]);
    }
}
