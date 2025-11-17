<?php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Http;

class AfricasTalkingProvider extends BaseSmsProvider
{
    protected string $baseUrl = 'https://api.africastalking.com/version1';

    public function getName(): string
    {
        return "Africa's Talking";
    }

    public function getConfigFields(): array
    {
        return [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'required' => true,
                'placeholder' => 'Enter your API Key',
                'help' => 'Your Africa\'s Talking API Key',
            ],
            'username' => [
                'label' => 'Username',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'sandbox',
                'help' => 'Your Africa\'s Talking username',
            ],
            'sender_id' => [
                'label' => 'Sender ID',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'RADMAN',
                'help' => 'Sender ID (max 11 characters)',
                'maxlength' => 11,
            ],
        ];
    }

    public function send(string $phoneNumber, string $message): bool
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber, true);

            $response = Http::withHeaders([
                'apiKey' => $this->config['api_key'],
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ])->asForm()->post("{$this->baseUrl}/messaging", [
                'username' => $this->config['username'],
                'to' => $formattedPhone,
                'message' => $message,
                'from' => $this->config['sender_id'] ?? null,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['SMSMessageData']['Recipients'][0]['status']) && 
                    $result['SMSMessageData']['Recipients'][0]['status'] === 'Success') {
                    $this->logInfo("SMS sent successfully", ['phone' => $formattedPhone]);
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            $this->logError("Exception: " . $e->getMessage());
            return false;
        }
    }

    public function sendBulk(array $recipients): array
    {
        $results = [];
        foreach ($recipients as $recipient) {
            $results[] = $this->send($recipient['phone'], $recipient['message']);
        }
        return $results;
    }

    public function getBalance(): ?float
    {
        try {
            $response = Http::withHeaders([
                'apiKey' => $this->config['api_key'],
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/user", [
                'username' => $this->config['username'],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['UserData']['balance'])) {
                    return (float) str_replace(['KES', ' '], '', $result['UserData']['balance']);
                }
            }

            return null;
        } catch (\Exception $e) {
            $this->logError("Get balance exception: " . $e->getMessage());
            return null;
        }
    }

    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'apiKey' => $this->config['api_key'],
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/user", [
                'username' => $this->config['username'],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['UserData'])) {
                    return [
                        'success' => true,
                        'message' => 'Connection successful',
                        'data' => [
                            'balance' => $result['UserData']['balance'] ?? 'N/A',
                        ],
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Connection failed. Please check your credentials.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }
}