<?php

namespace App\Services;

use App\Models\SmsSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SmsService
{
    protected $apiKey;
    protected $senderId;
    protected $serviceId;
    protected $baseUrl = 'https://smsapp.simflix.co.ke/sms/v3';

    public function __construct()
    {
        $this->apiKey = SmsSettings::get('sms_api_key');
        $this->senderId = SmsSettings::get('sms_sender_id', 'RADMAN');
        $this->serviceId = SmsSettings::get('sms_service_id', '0');
    }

    /**
     * Send SMS to a single recipient
     */
    public function send(string $phoneNumber, string $message): bool
    {
        try {
            if (empty($this->apiKey)) {
                Log::error('SMS API Key not configured');
                return false;
            }

            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            $response = Http::timeout(30)->post("{$this->baseUrl}/sendsms", [
                'api_key' => $this->apiKey,
                'service_id' => $this->serviceId,
                'mobile' => $formattedPhone,
                'response_type' => 'json',
                'shortcode' => $this->senderId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result[0]['status_code']) && $result[0]['status_code'] == '1000') {
                    Log::info("SMS sent successfully", [
                        'phone' => $formattedPhone,
                        'message_id' => $result[0]['message_id'] ?? null,
                        'cost' => $result[0]['message_cost'] ?? null,
                    ]);
                    return true;
                }
                
                Log::warning("SMS sending failed", [
                    'phone' => $formattedPhone,
                    'status_code' => $result[0]['status_code'] ?? 'unknown',
                    'status_desc' => $result[0]['status_desc'] ?? 'unknown',
                ]);
            }

            Log::error("SMS API request failed", [
                'phone' => $formattedPhone,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('SMS sending exception: ' . $e->getMessage(), [
                'phone' => $phoneNumber,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send bulk SMS to multiple recipients
     */
    public function sendBulk(array $recipients): array
    {
        try {
            if (empty($this->apiKey)) {
                Log::error('SMS API Key not configured');
                return [];
            }

            $messages = [];
            foreach ($recipients as $index => $recipient) {
                $messages[] = [
                    'mobile' => $this->formatPhoneNumber($recipient['phone']),
                    'message' => $recipient['message'],
                    'client_ref' => $recipient['id'] ?? $index,
                ];
            }

            $response = Http::timeout(30)->post("{$this->baseUrl}/sendmultiple", [
                'api_key' => $this->apiKey,
                'serviceId' => $this->serviceId,
                'from' => $this->senderId,
                'messages' => $messages,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Bulk SMS sending exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get account profile and balance
     */
    public function getProfile(): ?array
    {
        try {
            if (empty($this->apiKey)) {
                return null;
            }

            $response = Http::timeout(30)->post("{$this->baseUrl}/profile", [
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Get profile exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get SMS credit balance (cached for 5 minutes)
     */
    public function getBalance(): ?float
    {
        return Cache::remember('sms_balance', 300, function () {
            $profile = $this->getProfile();
            
            if ($profile && isset($profile[0]['wallet']['credit_balance'])) {
                return (float) $profile[0]['wallet']['credit_balance'];
            }
            
            return null;
        });
    }

    /**
     * Format phone number for Kenya (+254)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($phone, '+254')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '254')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }

        if (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            return '254' . $phone;
        }

        return $phone;
    }
}