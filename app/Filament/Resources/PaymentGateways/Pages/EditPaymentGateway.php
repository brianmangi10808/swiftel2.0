<?php

namespace App\Filament\Resources\PaymentGateways\Pages;

use App\Filament\Resources\PaymentGateways\PaymentGatewayResource;
use App\Services\SystemPayHeroService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class EditPaymentGateway extends EditRecord
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        
        if ($record->gateway_type === 'mpesa' && $record->mpesaConfig) {
            $configData = $record->mpesaConfig->toArray();
            $data['short_code'] = $configData['short_code'] ?? '';
            $data['passkey'] = $configData['passkey'] ?? '';
            $data['consumer_key'] = $configData['consumer_key'] ?? '';
            $data['consumer_secret'] = $configData['consumer_secret'] ?? '';
        } elseif ($record->gateway_type === 'payhero' && $record->payheroConfig) {
            $configData = $record->payheroConfig->toArray();
            $data['basic_auth'] = $configData['basic_auth'] ?? '';
            $data['api_username'] = $configData['api_username'] ?? '';
            $data['account_id'] = $configData['account_id'] ?? '';
            $data['api_password'] = $configData['api_password'] ?? '';
        } elseif ($record->gateway_type === 'bank' && $record->bankConfig) {
            $configData = $record->bankConfig->toArray();
            $data['bank_name'] = $configData['bank_name'] ?? '';
            $data['bank_paybill'] = $configData['bank_paybill'] ?? '';
            $data['bank_account_number'] = $configData['bank_account_number'] ?? '';
            $data['channel_id'] = $configData['channel_id'] ?? '';
        }
        
        return $data;
    }
    
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Update main gateway record
        $record->update([
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'is_default' => $data['is_default'],
        ]);
        
        // Update gateway-specific configuration
        if ($record->gateway_type === 'mpesa') {
            $record->mpesaConfig()->updateOrCreate(
                ['gateway_id' => $record->id],
                [
                    'short_code' => $data['short_code'],
                    'company_id' => $record->company_id,
                    'passkey' => $data['passkey'],
                    'consumer_key' => $data['consumer_key'],
                    'consumer_secret' => $data['consumer_secret'],
                ]
            );
        } elseif ($record->gateway_type === 'payhero') {
            $record->payheroConfig()->updateOrCreate(
                ['gateway_id' => $record->id],
                [
                    'basic_auth' => $data['basic_auth'],
                    'company_id' => $record->company_id,
                    'api_username' => $data['api_username'],
                    'account_id' => $data['account_id'],
                    'api_password' => $data['api_password'],
                ]
            );
        } elseif ($record->gateway_type === 'bank') {
            $currentConfig = $record->bankConfig;
            
            // Check if bank details changed
            $bankDetailsChanged = $this->bankDetailsChanged($currentConfig, $data);
            
            // If details changed, re-register with PayHero
            if ($bankDetailsChanged) {
                $newChannelId = $this->registerBankWithPayHero($data);
                
                if ($newChannelId) {
                    $record->bankConfig()->updateOrCreate(
                        ['gateway_id' => $record->id],
                        [
                            'bank_name' => $data['bank_name'],
                            'company_id' => $record->company_id,
                            'bank_paybill' => $data['bank_paybill'],
                            'bank_account_number' => $data['bank_account_number'],
                            'channel_id' => $newChannelId,
                        ]
                    );
                    
                    Notification::make()
                        ->success()
                        ->title('Bank Re-registered Successfully')
                        ->body("New Channel ID: {$newChannelId}")
                        ->send();
                } else {
                    Notification::make()
                        ->danger()
                        ->title('Bank Re-registration Failed')
                        ->body('Could not register new bank details. Keeping existing configuration.')
                        ->send();
                    
                    $record->bankConfig()->updateOrCreate(
                        ['gateway_id' => $record->id],
                        [
                            'bank_name' => $data['bank_name'],
                            'bank_paybill' => $data['bank_paybill'],
                            'bank_account_number' => $data['bank_account_number'],
                            'channel_id' => $data['channel_id'] ?? null,
                        ]
                    );
                }
            } else {
                $record->bankConfig()->updateOrCreate(
                    ['gateway_id' => $record->id],
                    [
                        'bank_name' => $data['bank_name'],
                        'bank_paybill' => $data['bank_paybill'],
                        'bank_account_number' => $data['bank_account_number'],
                        'channel_id' => $data['channel_id'] ?? null,
                    ]
                );
            }
        }
        
        return $record;
    }
    
    /**
     * Register bank channel with PayHero API using SYSTEM service
     */
    protected function registerBankWithPayHero(array $data): ?string
    {
        try {
            $systemPayhero = app(SystemPayHeroService::class);
            
            $channelId = $systemPayhero->registerBankChannel(
                bankName: $data['bank_name'],
                paybill: $data['bank_paybill'],
                accountNumber: $data['bank_account_number']
            );
            
            if ($channelId) {
                return $channelId;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Bank Re-registration Error', [
                'message' => $e->getMessage(),
                'bank_name' => $data['bank_name']
            ]);
            
            return null;
        }
    }
    
    /**
     * Check if bank details have changed
     */
    protected function bankDetailsChanged($currentConfig, array $data): bool
    {
        if (!$currentConfig) {
            return true;
        }
        
        return ($currentConfig->bank_paybill ?? '') != ($data['bank_paybill'] ?? '') ||
               ($currentConfig->bank_account_number ?? '') != ($data['bank_account_number'] ?? '') ||
               ($currentConfig->bank_name ?? '') != ($data['bank_name'] ?? '');
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Gateway Updated')
            ->body('The payment gateway configuration has been updated successfully.')
            ->send();
    }
}