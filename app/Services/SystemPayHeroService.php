<?php

namespace App\Services;

use App\Models\SystemPayments;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SystemPayHeroService
{
    protected $basicAuth;
    protected $accountId;
    protected $apiUrl;
    protected $client;
    
    public function __construct()
    {
        $this->basicAuth = SystemPayments::getValue('system_payhero_basic_auth');
        $this->accountId = SystemPayments::getValue('system_payhero_account_id', 9756);
        $this->apiUrl = SystemPayments::getValue('system_payhero_api_url', 'https://backend.payhero.co.ke/api/v2');
        
        $this->client = new Client([
            'verify' => false,
            'timeout' => 30,
        ]);
    }
    
    public function registerBankChannel(string $bankName, string $paybill, string $accountNumber): ?string
    {
        try {
            $response = $this->client->post($this->apiUrl . '/payment_channels', [
                'headers' => [
                    'Authorization' => 'Basic ' . $this->basicAuth,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'channel_type' => 'bank',
                    'account_id' => (int) $this->accountId,
                    'short_code' => $paybill,
                    'account_number' => $accountNumber,
                    'description' => $bankName,
                ],
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            if (($response->getStatusCode() === 200 || $response->getStatusCode() === 201) && isset($result['id'])) {
                return (string) $result['id'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('System PayHero Bank Registration Failed', [
                'error' => $e->getMessage(),
                'bank_name' => $bankName
            ]);
            return null;
        }
    }
}