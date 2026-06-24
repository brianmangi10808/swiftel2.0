<?php

namespace App\Filament\Resources\PaymentGateways\Pages;

use App\Filament\Resources\PaymentGateways\PaymentGatewayResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Services\SystemPayHeroService;


class CreatePaymentGateway extends CreateRecord
{
    protected static string $resource = PaymentGatewayResource::class;
 protected function handleRecordCreation(array $data): Model
{
    $gatewayType = $data['gateway_type'];
    
    // ✅ For bank gateways, validate registration FIRST before creating anything
    if ($gatewayType === 'bank') {
        $channelId = $this->registerBankWithPayHero($data);
        
        if (!$channelId) {
            Notification::make()
                ->danger()
                ->title('Registration Failed')
                ->body('Could not register bank with PayHero. Gateway not created.')
                ->send();
            
            throw new \Exception('Bank registration failed: No channel ID received');
        }
        
        // Store channel ID to use after creating gateway
        $data['channel_id'] = $channelId;
    }
    
    // Create main gateway record
    $gateway = static::getModel()::create([
        'company_id' => $data['company_id'],
        'gateway_type' => $gatewayType,
        'name' => $data['name'],
        'is_active' => $data['is_active'] ?? true,
        'is_default' => $data['is_default'] ?? false,
    ]);
    
    // Create gateway-specific configuration
    if ($gatewayType === 'mpesa') {
        $gateway->mpesaConfig()->create([
            'company_id' => $data['company_id'], 
            'short_code' => $data['short_code'],
            'passkey' => $data['passkey'],
            'consumer_key' => $data['consumer_key'],
            'consumer_secret' => $data['consumer_secret'],
        ]);
    } elseif ($gatewayType === 'payhero') {
        $gateway->payheroConfig()->create([
            'company_id' => $data['company_id'], 
            'basic_auth' => $data['basic_auth'],
            'api_username' => $data['api_username'] ?? null,
            'account_id' => $data['account_id'] ?? null,
            'api_password' => $data['api_password'] ?? null,
        ]);
    } elseif ($gatewayType === 'bank') {
        // ✅ Use the pre-validated channel_id
        $gateway->bankConfig()->create([
            'company_id' => $data['company_id'], 
            'bank_name' => $data['bank_name'],
            'bank_paybill' => $data['bank_paybill'],
            'bank_account_number' => $data['bank_account_number'],
            'channel_id' => $data['channel_id'],
        ]);
        
        Notification::make()
            ->success()
            ->title('Bank Gateway Created')
            ->body("Bank registered successfully with Channel ID: {$data['channel_id']}")
            ->send();
    }
    
    return $gateway;
}
 protected function registerBankWithPayHero(array $data): ?string
{
    try {
        $systemPayhero = app(\App\Services\SystemPayHeroService::class);
        
        $channelId = $systemPayhero->registerBankChannel(
            bankName: $data['bank_name'],
            paybill: $data['bank_paybill'],
            accountNumber: $data['bank_account_number']
        );
        
        if ($channelId) {
            Notification::make()
                ->success()
                ->title('Bank Registered Successfully')
                ->body("Channel ID: {$channelId}")
                ->send();
                
            return $channelId;
        }
        
        Notification::make()
            ->warning()
            ->title('Bank Registration Failed')
            ->body('Could not register bank channel with PayHero.')
            ->send();
            
        return null;
        
    } catch (\Exception $e) {
        Log::error('Bank Registration Error', [
            'message' => $e->getMessage()
        ]);
        
        Notification::make()
            ->danger()
            ->title('Bank Registration Failed')
            ->body($e->getMessage())
            ->send();
            
        return null;
    }
}
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Gateway Created')
            ->body('The payment gateway has been configured successfully.');
    }
}