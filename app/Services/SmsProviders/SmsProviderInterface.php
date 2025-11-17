<?php

namespace App\Services\SmsProviders;

interface SmsProviderInterface
{
    /**
     * Get provider name
     */
    public function getName(): string;

    /**
     * Get required configuration fields
     */
    public function getConfigFields(): array;

    /**
     * Send single SMS
     */
    public function send(string $phoneNumber, string $message): bool;

    /**
     * Send bulk SMS
     */
    public function sendBulk(array $recipients): array;

    /**
     * Get account balance
     */
    public function getBalance(): ?float;

    /**
     * Test connection
     */
    public function testConnection(): array;
}