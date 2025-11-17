<?php

namespace App\Filament\Pages;

use App\Models\SmsSettings;
use App\Services\SmsService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings';
    protected static ?string $title = 'Settings';
    protected static bool $shouldRegisterNavigation = false;
    
    public string $activeTab = 'general';
    
    // General Settings
    public ?string $company_name = null;
    public ?string $system_email = null;
    public ?string $paybill = null;
    public ?string $till_number = null;
    
    // SMS Gateway Settings
    public ?string $sms_provider = 'simflix';
    public ?string $api_key = null;
    public ?string $sender_id = null;
    public ?string $service_id = '0';
    
    // Notification Settings
    public bool $payment_confirmation_enabled = false;
    public ?string $payment_confirmation_template = null;
    public bool $expiry_notification_enabled = false;
    public ?string $expiry_notification_template = null;
    public bool $expiry_reminder_enabled = false;
    public ?string $expiry_reminder_template = null;

    public function mount(): void
    {
        // Load settings from database
        $this->company_name = SmsSettings::get('company_name');
        $this->system_email = SmsSettings::get('system_email');
        $this->paybill = SmsSettings::get('paybill');
        $this->till_number = SmsSettings::get('till_number');
        
        $this->sms_provider = SmsSettings::get('sms_provider', 'simflix');
        $this->api_key = SmsSettings::get('sms_api_key');
        $this->sender_id = SmsSettings::get('sms_sender_id');
        $this->service_id = SmsSettings::get('sms_service_id', '0');
        
        $this->payment_confirmation_enabled = SmsSettings::isEnabled('payment_confirmation_enabled');
        $this->payment_confirmation_template = SmsSettings::get('payment_confirmation_template');
        $this->expiry_notification_enabled = SmsSettings::isEnabled('expiry_notification_enabled');
        $this->expiry_notification_template = SmsSettings::get('expiry_notification_template');
        $this->expiry_reminder_enabled = SmsSettings::isEnabled('expiry_reminder_enabled');
        $this->expiry_reminder_template = SmsSettings::get('expiry_reminder_template');
    }

    public function saveGeneral(): void
    {
        SmsSettings::set('company_name', $this->company_name);
        SmsSettings::set('system_email', $this->system_email);
        SmsSettings::set('paybill', $this->paybill);
        SmsSettings::set('till_number', $this->till_number);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    public function saveSmsGateway(): void
    {
        SmsSettings::set('sms_provider', $this->sms_provider);
        SmsSettings::set('sms_api_key', $this->api_key);
        SmsSettings::set('sms_sender_id', $this->sender_id);
        SmsSettings::set('sms_service_id', $this->service_id);

        Notification::make()
            ->title('SMS Gateway settings saved successfully')
            ->success()
            ->send();
    }

    public function testSmsConnection(): void
    {
        try {
            $smsService = new SmsService();
            $profile = $smsService->getProfile();

            if ($profile && isset($profile[0]['status_code']) && $profile[0]['status_code'] == '1000') {
                $balance = $profile[0]['wallet']['credit_balance'] ?? 'N/A';
                $company = $profile[0]['partner']['company'] ?? 'N/A';
                
                Notification::make()
                    ->title('Connection successful!')
                    ->body("Company: {$company}<br>Credit Balance: KES {$balance}")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Connection failed')
                    ->body('Unable to connect to SMS gateway. Please check your API Key.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Connection error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function saveNotifications(): void
    {
        SmsSettings::set('payment_confirmation_enabled', $this->payment_confirmation_enabled ? '1' : '0');
        SmsSettings::set('payment_confirmation_template', $this->payment_confirmation_template);
        
        SmsSettings::set('expiry_notification_enabled', $this->expiry_notification_enabled ? '1' : '0');
        SmsSettings::set('expiry_notification_template', $this->expiry_notification_template);
        
        SmsSettings::set('expiry_reminder_enabled', $this->expiry_reminder_enabled ? '1' : '0');
        SmsSettings::set('expiry_reminder_template', $this->expiry_reminder_template);

        Notification::make()
            ->title('Notification settings saved successfully')
            ->success()
            ->send();
    }

    public static function getSlug(): string
    {
        return 'settings';
    }
}