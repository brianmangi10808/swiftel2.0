<?php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Http;

class TwilioProvider extends BaseSmsProvider
{
    public function getName(): string
    {
        return 'Twilio';
    }

    public function getConfigFields(): array
    {
        return [
            'account_sid' => [
                'label' => 'Account SID',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'ACxxxxxxxxxxxxxxxxxxxxx',
                'help' => 'Your Twilio Account SID',
            ],
            'auth_token' => [
                'label' => 'Auth Token',
                'type' => 'password',
                'required' => true,
                'placeholder' => 'Enter your Auth Token',
                'help' => 'Your Twilio Auth Token',
            ],
            'sender_id' => [
                'label' => 'Phone Number',
                'type' => 'text',
                'required' => true,
                'placeholder' => '+254700000000',
                'help' => 'Your Twilio phone number (with country code)',
            ],
        ];
    }

    public function send(string $phoneNumber, string $message): bool
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber, true);
            $accountSid = $this->config['account_sid'];
            $authToken = $this->config['auth_token'];

            $response = Http::withBasicAuth($accountSid, $authToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                    'From' => $this->config['sender_id'],
                    'To' => $formattedPhone,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $this->logInfo("SMS sent successfully", ['phone' => $formattedPhone]);
                return true;
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
            $accountSid = $this->config['account_sid'];
            $authToken = $this->config['auth_token'];

            $response = Http::withBasicAuth($accountSid, $authToken)
                ->get("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Balance.json");

            if ($response->successful()) {
                $result = $response->json();
                return isset($result['balance']) ? (float) $result['balance'] : null;
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
            $accountSid = $this->config['account_sid'];
            $authToken = $this->config['auth_token'];

            $response = Http::withBasicAuth($accountSid, $authToken)
                ->get("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}.json");

            if ($response->successful()) {
                $result = $response->json();
                
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'data' => [
                        'account' => $result['friendly_name'] ?? 'N/A',
                        'status' => $result['status'] ?? 'N/A',
                    ],
                ];
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