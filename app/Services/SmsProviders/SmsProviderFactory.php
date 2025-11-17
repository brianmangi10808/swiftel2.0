<?php

namespace App\Services\SmsProviders;

use App\Models\SmsSettings;
use App\Models\SmsProvider;

class SmsProviderFactory
{
    /**
     * Get all available provider classes
     */
    public static function getAvailableProviders(): array
    {
        return [
            'simflix' => SimflixProvider::class,
            'africastalking' => AfricasTalkingProvider::class,
            'twilio' => TwilioProvider::class,
            'suftech' => SuftechProvider::class,
        ];
    }

    /**
     * Get provider instance from database or fallback to legacy settings
     *
     * @param int|string|null $provider Provider ID, type, or null for default
     */
    public static function make($provider = null): ?SmsProviderInterface
    {
        // Try to get from database first
        if (is_numeric($provider)) {
            // Provider ID passed
            $dbProvider = SmsProvider::find($provider);
        } elseif (is_string($provider)) {
            // Provider type passed
            $dbProvider = SmsProvider::getByType($provider);
        } else {
            // Get default provider
            $dbProvider = SmsProvider::getDefault();
        }

        // If database provider found, use it
        if ($dbProvider) {
            return self::makeFromDbProvider($dbProvider);
        }

        // Fallback to legacy settings for backward compatibility
        return self::makeFromLegacySettings($provider);
    }

    /**
     * Create provider instance from database model
     */
    public static function makeFromDbProvider(SmsProvider $dbProvider): ?SmsProviderInterface
    {
        $providers = self::getAvailableProviders();

        if (!isset($providers[$dbProvider->provider_type])) {
            return null;
        }

        $providerClass = $providers[$dbProvider->provider_type];
        return new $providerClass($dbProvider->configuration ?? []);
    }

    /**
     * Create provider instance from legacy settings (backward compatibility)
     */
    protected static function makeFromLegacySettings(?string $provider = null): ?SmsProviderInterface
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
     * Get provider configuration from legacy settings
     */
    protected static function getProviderConfig(string $provider): array
    {
        $providers = self::getAvailableProviders();

        if (!isset($providers[$provider])) {
            return [];
        }

        $providerClass = $providers[$provider];
        $instance = new $providerClass([]);

        $fields = $instance->getConfigFields();
        $config = [];

        foreach ($fields as $key => $field) {
            // Try provider-specific key first (e.g., sms_africastalking_api_key)
            $value = SmsSettings::get("sms_{$provider}_{$key}", null);

            // Fallback to generic key (e.g., sms_api_key) for backward compatibility
            if ($value === null) {
                $value = SmsSettings::get("sms_{$key}", $field['default'] ?? null);
            }

            $config[$key] = $value;
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
            'suftech' => 'Suftech',
        ];

        return $providers[$provider] ?? $provider;
    }

    /**
     * Get all active providers from database
     */
    public static function getAllActive(): array
    {
        $providers = [];
        $dbProviders = SmsProvider::getActive();

        foreach ($dbProviders as $dbProvider) {
            $instance = self::makeFromDbProvider($dbProvider);
            if ($instance) {
                $providers[$dbProvider->id] = [
                    'instance' => $instance,
                    'model' => $dbProvider,
                ];
            }
        }

        return $providers;
    }
}