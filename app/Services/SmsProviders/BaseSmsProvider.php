<?php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Log;

abstract class BaseSmsProvider implements SmsProviderInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Format phone number for Kenya (+254)
     */
    protected function formatPhoneNumber(string $phone, bool $withPlus = false): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($phone, '+254')) {
            $formatted = substr($phone, 1);
        } elseif (str_starts_with($phone, '254')) {
            $formatted = $phone;
        } elseif (str_starts_with($phone, '0')) {
            $formatted = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $formatted = '254' . $phone;
        } else {
            $formatted = $phone;
        }

        return $withPlus ? '+' . $formatted : $formatted;
    }

    /**
     * Log error
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error("[{$this->getName()}] {$message}", $context);
    }

    /**
     * Log info
     */
    protected function logInfo(string $message, array $context = []): void
    {
        Log::info("[{$this->getName()}] {$message}", $context);
    }
}