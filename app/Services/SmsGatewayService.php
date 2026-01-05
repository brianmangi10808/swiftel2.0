<?php

namespace App\Services;

use App\Models\SmsGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class SmsGatewayService
{
   
  public function sendSms(string $phone, string $message, ?int $companyId = null): bool
{
    // ✅ Auto-detect company if not passed explicitly
    $companyId ??= Auth::user()?->company_id;

    if (! $companyId) {
        throw new \Exception('Company context missing for SMS gateway.');
    }

    // ✅ STRICT company-scoped active gateway
    $gateway = SmsGateway::where('company_id', $companyId)
        ->where('is_active', true)
        ->first();

    if (! $gateway) {
        throw new \Exception('No active SMS gateway configured for this company.');
    }

    $type = $gateway->type;
    $cred = $gateway->credentials ?? [];

    $success = match ($type) {
        'simflix'         => $this->sendViaSimflix($cred, $phone, $message),
        'africastalking'  => $this->sendViaAfricasTalking($cred, $phone, $message),
        'afrokatt'        => $this->sendViaAfrokatt($cred, $phone, $message),
        'custom'          => $this->sendViaCustom($cred, $phone, $message),
        default           => false,
    };

 // In SmsGatewayService::sendSms
SmsLogger::log(
    $phone,
    $message,
    $type,
    $success ? 'sent' : 'failed',
    $gateway->company_id   
);


    return $success;
}


    /**
     * SIMFLIX implementation
     */
    protected function sendViaSimflix(array $cred, string $phone, string $message): bool
    {
        if (! isset($cred['url'], $cred['api_key'], $cred['sender_id'])) {
            throw new \Exception('SimFlix credentials are incomplete.');
        }

        $response = Http::post($cred['url'], [
            'api_key'       => $cred['api_key'],
            'service_id'    => 0,
            'mobile'        => $phone,
            'response_type' => 'json',
            'shortcode'     => $cred['sender_id'],
            'message'       => $message,
            // 'date_send'   => optional – you can add scheduling later
        ]);

        if (! $response->ok()) {
            return false;
        }

        $json = $response->json();

        // According to SimFlix docs: status_code = "1000" => Success
        return isset($json[0]['status_code']) && $json[0]['status_code'] === '1000';
    }

    /**
     * Africa's Talking – placeholder
     */
    protected function sendViaAfricasTalking(array $cred, string $phone, string $message): bool
    {
        // TODO: implement real AT logic here later
        return true;
    }

    /**
     * Afrokatt – placeholder
     */
    protected function sendViaAfrokatt(array $cred, string $phone, string $message): bool
    {
        // TODO: implement real Afrokatt logic here later
        return true;
    }

    /**
     * Custom gateway – placeholder
     */
    protected function sendViaCustom(array $cred, string $phone, string $message): bool
    {
        // TODO: implement custom webhook / API logic here later
        return true;
    }
}
