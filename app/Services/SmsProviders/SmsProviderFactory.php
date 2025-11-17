<?php

namespace App\Services\SmsProviders;

use App\Models\SmsSettings;

class SmsProviderFactory
{
    /**
     * Get all available providers
     */
    public static function getAvailableProviders(): array
    {
        return [
            'simflix' => SimflixProvider::class,
            'africastalking' => AfricasTalkingProvider::class,
            'twilio' => TwilioProvider::class,
        ];
    }

    /**
     * Get provider instance from settings
     */
    public static function make(?string $provider = null): ?SmsProviderInterface
    {
        $providerKey = $provider ?? SmsSettings::get('sms_provider', 'simflix');
        $providers = self::getAvailableProviders();

        if (!isset($providers[$providerKey])) {
            return null;
        }

        $providerClass = $providers[$providerKey];
        $config = self::getProviderConfig($providerKey);

        return new $providerClass($config);
    }

    /**
     * Get provider configuration from database
     */
    protected static function getProviderConfig(string $provider): array
    {
        $prefix = "sms_{$provider}_";
        $instance = new ($provider == 'simflix' ? SimflixProvider::class : 
                        ($provider == 'africastalking' ? AfricasTalkingProvider::class : TwilioProvider::class))([]);
        
        $fields = $instance->getConfigFields();
        $config = [];

        foreach ($fields as $key => $field) {
            $config[$key] = SmsSettings::get($prefix . $key, $field['default'] ?? null);
        }

        return $config;
    }

    /**
     * Get provider display name
     */
    public static function getProviderName(string $provider): string
    {
        $providers = [
            'simflix' => 'Simflix',
            'africastalking' => "Africa's Talking",
            'twilio' => 'Twilio',
        ];

        return $providers[$provider] ?? $provider;
    }
}