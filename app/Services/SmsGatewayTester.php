<?php

namespace App\Services;

use App\Models\SmsGateway;
use Illuminate\Support\Facades\Http;

class SmsGatewayTester
{
    public static function test(SmsGateway $gateway)
    {
        $creds = $gateway->credentials;
        $type  = $gateway->type;

        return match ($type) {

            'simflix' => self::testSimFlix($creds),

            'africastalking' => self::testAfricasTalking($creds),

            'afrokatt' => self::testAfrokatt($creds),

            default => throw new \Exception("Testing not supported for this gateway type."),
        };
    }

    // -----------------------------------
    // SIMFLIX TEST
    // -----------------------------------
protected static function testSimFlix(array $creds)
{
    // Normalize the URL
    $url = rtrim($creds['url'], '/');

    // Call the SimFlix profile endpoint
    $response = Http::post('https://smsapp.simflix.co.ke/sms/v3/profile', [
        'api_key' => $creds['api_key'],
    ]);

    // Handle request errors
    if ($response->failed()) {
        throw new \Exception("SimFlix Error: " . $response->body());
    }

    $json = $response->json();

    // Validate structure
    if (!isset($json[0]['wallet']['credit_balance'])) {
        throw new \Exception("SimFlix Error: Unexpected response format");
    }

    return $json[0]['wallet']['credit_balance'];
}


    // -----------------------------------
    // AFRICA'S TALKING
    // -----------------------------------
    protected static function testAfricasTalking(array $creds)
    {
        $response = Http::withHeaders([
            'apiKey' => $creds['api_key'],
        ])->get("https://api.africastalking.com/version1/user?username={$creds['username']}");

        if ($response->failed()) {
            throw new \Exception("Africa's Talking Error: " . $response->body());
        }

        return $response->json()['User']['balance'] ?? 'Unknown';
    }

    // -----------------------------------
    // AFROKATT
    // (Fictional example — adjust when you get their real API)
    // -----------------------------------
    protected static function testAfrokatt(array $creds)
    {
        $response = Http::withToken($creds['api_key'])
                        ->get("https://api.afrokatt.com/balance");

        if ($response->failed()) {
            throw new \Exception("Afrokatt Error: " . $response->body());
        }

        return $response->json()['balance'] ?? 'Unknown';
    }
}
