<?php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Http;

class SimflixProvider extends BaseSmsProvider
{
    protected string $baseUrl = 'https://smsapp.simflix.co.ke/sms/v3';

    public function getName(): string
    {
        return 'Simflix';
    }

    public function getConfigFields(): array
    {
        return [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'required' => true,
                'placeholder' => 'Enter your Simflix API Key',
                'help' => 'Your Simflix API authentication key',
            ],
            'sender_id' => [
                'label' => 'Sender ID',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'RADMAN',
                'help' => 'Sender ID (max 11 characters)',
                'maxlength' => 11,
            ],
            'service_id' => [
                'label' => 'Service ID',
                'type' => 'text',
                'required' => false,
                'placeholder' => '0',
                'help' => 'Usually 0 for default service',
                'default' => '0',
            ],
        ];
    }

    public function send(string $phoneNumber, string $message): bool
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            $response = Http::timeout(30)->post("{$this->baseUrl}/sendsms", [
                'api_key' => $this->config['api_key'],
                'service_id' => $this->config['service_id'] ?? '0',
                'mobile' => $formattedPhone,
                'response_type' => 'json',
                'shortcode' => $this->config['sender_id'],
                'message' => $message,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result[0]['status_code']) && $result[0]['status_code'] == '1000') {
                    $this->logInfo("SMS sent successfully", [
                        'phone' => $formattedPhone,
                        'message_id' => $result[0]['message_id'] ?? null,
                    ]);
                    return true;
                }
                
                $this->logError("SMS sending failed", [
                    'phone' => $formattedPhone,
                    'response' => $result,
                ]);
            }

            return false;
        } catch (\Exception $e) {
            $this->logError("Exception: " . $e->getMessage());
            return false;
        }
    }

    public function sendBulk(array $recipients): array
    {
        try {
            $messages = [];
            foreach ($recipients as $index => $recipient) {
                $messages[] = [
                    'mobile' => $this->formatPhoneNumber($recipient['phone']),
                    'message' => $recipient['message'],
                    'client_ref' => $recipient['id'] ?? $index,
                ];
            }

            $response = Http::timeout(30)->post("{$this->baseUrl}/sendmultiple", [
                'api_key' => $this->config['api_key'],
                'serviceId' => $this->config['service_id'] ?? '0',
                'from' => $this->config['sender_id'],
                'messages' => $messages,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            $this->logError("Bulk SMS exception: " . $e->getMessage());
            return [];
        }
    }

    public function getBalance(): ?float
    {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/profile", [
                'api_key' => $this->config['api_key'],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result[0]['wallet']['credit_balance'])) {
                    return (float) $result[0]['wallet']['credit_balance'];
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
            $response = Http::timeout(30)->post("{$this->baseUrl}/profile", [
                'api_key' => $this->config['api_key'],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result[0]['status_code']) && $result[0]['status_code'] == '1000') {
                    return [
                        'success' => true,
                        'message' => 'Connection successful',
                        'data' => [
                            'company' => $result[0]['partner']['company'] ?? 'N/A',
                            'balance' => $result[0]['wallet']['credit_balance'] ?? 'N/A',
                            'account' => $result[0]['wallet']['account_number'] ?? 'N/A',
                        ],
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Connection failed. Please check your API credentials.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }
}