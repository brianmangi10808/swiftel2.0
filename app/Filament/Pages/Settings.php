<?php

namespace App\Filament\Pages;

use App\Models\SmsSettings;
use App\Models\SmsProvider;
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

    // Notification Settings
    public bool $payment_confirmation_enabled = false;
    public ?string $payment_confirmation_template = null;
    public bool $expiry_notification_enabled = false;
    public ?string $expiry_notification_template = null;
    public bool $expiry_reminder_enabled = false;
    public ?string $expiry_reminder_template = null;

    public function mount(): void
    {
        // Load general settings
        $this->company_name = SmsSettings::get('company_name');
        $this->system_email = SmsSettings::get('system_email');
        $this->paybill = SmsSettings::get('paybill');
        $this->till_number = SmsSettings::get('till_number');

        // Load notification settings
        $this->payment_confirmation_enabled = SmsSettings::isEnabled('payment_confirmation_enabled');
        $this->payment_confirmation_template = SmsSettings::get('payment_confirmation_template');
        $this->expiry_notification_enabled = SmsSettings::isEnabled('expiry_notification_enabled');
        $this->expiry_notification_template = SmsSettings::get('expiry_notification_template');
        $this->expiry_reminder_enabled = SmsSettings::isEnabled('expiry_reminder_enabled');
        $this->expiry_reminder_template = SmsSettings::get('expiry_reminder_template');
    }

    public function getSmsProviders()
    {
        return SmsProvider::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();
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