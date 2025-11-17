<?php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Http;

class SuftechProvider extends BaseSmsProvider
{
    protected string $baseUrl = 'https://api.suftech.com/v1'; // Default URL, can be overridden in config

    public function getName(): string
    {
        return 'Suftech';
    }

    public function getConfigFields(): array
    {
        return [
            'api_url' => [
                'label' => 'API URL',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'https://api.suftech.com/v1',
                'help' => 'Suftech API base URL',
                'default' => 'https://api.suftech.com/v1',
            ],
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'required' => true,
                'placeholder' => 'Enter your API Key',
                'help' => 'Your Suftech API Key',
            ],
            'sender_id' => [
                'label' => 'Sender ID',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'RADMAN',
                'help' => 'Sender ID (max 11 characters)',
                'maxlength' => 11,
            ],
            'client_id' => [
                'label' => 'Client ID',
                'type' => 'text',
                'required' => false,
                'placeholder' => 'Optional Client ID',
                'help' => 'Optional Client ID for tracking',
            ],
        ];
    }

    public function send(string $phoneNumber, string $message): bool
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber, false);

            // Override base URL if provided in config
            $apiUrl = $this->config['api_url'] ?? $this->baseUrl;

            $payload = [
                'api_key' => $this->config['api_key'],
                'sender_id' => $this->config['sender_id'],
                'phone' => $formattedPhone,
                'message' => $message,
            ];

            // Add optional client_id if provided
            if (!empty($this->config['client_id'])) {
                $payload['client_id'] = $this->config['client_id'];
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$apiUrl}/sms/send", $payload);

            if ($response->successful()) {
                $result = $response->json();

                // Check for success response (adjust based on Suftech's actual API response)
                if (isset($result['status']) && $result['status'] === 'success') {
                    $this->logInfo("SMS sent successfully", ['phone' => $formattedPhone]);
                    return true;
                }

                // Log error if status is not success
                $this->logError("SMS sending failed", [
                    'phone' => $formattedPhone,
                    'response' => $result
                ]);
                return false;
            }

            $this->logError("HTTP request failed", [
                'phone' => $formattedPhone,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            $this->logError("Exception: " . $e->getMessage());
            return false;
        }
    }

    public function sendBulk(array $recipients): array
    {
        $results = [];

        // Try bulk endpoint if available
        try {
            $apiUrl = $this->config['api_url'] ?? $this->baseUrl;

            $messages = array_map(function($recipient) {
                return [
                    'phone' => $this->formatPhoneNumber($recipient['phone'], false),
                    'message' => $recipient['message'],
                ];
            }, $recipients);

            $payload = [
                'api_key' => $this->config['api_key'],
                'sender_id' => $this->config['sender_id'],
                'messages' => $messages,
            ];

            if (!empty($this->config['client_id'])) {
                $payload['client_id'] = $this->config['client_id'];
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$apiUrl}/sms/send-bulk", $payload);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'success') {
                    // Return success for all if bulk succeeded
                    return array_fill(0, count($recipients), true);
                }
            }

            // If bulk endpoint fails, fall back to individual sends
            $this->logInfo("Bulk endpoint failed, falling back to individual sends");

        } catch (\Exception $e) {
            $this->logError("Bulk send exception, falling back: " . $e->getMessage());
        }

        // Fallback: send individually
        foreach ($recipients as $recipient) {
            $results[] = $this->send($recipient['phone'], $recipient['message']);
        }

        return $results;
    }

    public function getBalance(): ?float
    {
        try {
            $apiUrl = $this->config['api_url'] ?? $this->baseUrl;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$apiUrl}/account/balance", [
                'api_key' => $this->config['api_key'],
            ]);

            if ($response->successful()) {
                $result = $response->json();

                // Adjust based on actual Suftech API response format
                if (isset($result['balance'])) {
                    return (float) $result['balance'];
                }

                if (isset($result['data']['balance'])) {
                    return (float) $result['data']['balance'];
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
            $apiUrl = $this->config['api_url'] ?? $this->baseUrl;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$apiUrl}/account/verify", [
                'api_key' => $this->config['api_key'],
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'success') {
                    $balance = $this->getBalance();

                    return [
                        'success' => true,
                        'message' => 'Connection successful',
                        'data' => [
                            'balance' => $balance ? 'KES ' . number_format($balance, 2) : 'N/A',
                            'sender_id' => $this->config['sender_id'] ?? 'N/A',
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
