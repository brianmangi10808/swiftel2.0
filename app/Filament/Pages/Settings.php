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
    public ?string $company_logo = null;
    public ?string $system_email = null;
    public ?string $payment_type = 'paybill';
    public ?string $paybill = null;
    public ?string $till_number = null;
    public ?string $payment_phone = null;
    public ?string $support_phone = null;
    public ?string $support_email = null;

    // Appearance Settings
    public ?string $primary_color = '#f59e0b';
    public ?string $secondary_color = '#10b981';
    public ?string $font_family = 'Inter';

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
        $this->company_logo = SmsSettings::get('company_logo');
        $this->system_email = SmsSettings::get('system_email');
        $this->payment_type = SmsSettings::get('payment_type', 'paybill');
        $this->paybill = SmsSettings::get('paybill');
        $this->till_number = SmsSettings::get('till_number');
        $this->payment_phone = SmsSettings::get('payment_phone');
        $this->support_phone = SmsSettings::get('support_phone');
        $this->support_email = SmsSettings::get('support_email');

        // Load appearance settings
        $this->primary_color = SmsSettings::get('primary_color', '#f59e0b');
        $this->secondary_color = SmsSettings::get('secondary_color', '#10b981');
        $this->font_family = SmsSettings::get('font_family', 'Inter');

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
        SmsSettings::set('payment_type', $this->payment_type);
        SmsSettings::set('paybill', $this->paybill);
        SmsSettings::set('till_number', $this->till_number);
        SmsSettings::set('payment_phone', $this->payment_phone);
        SmsSettings::set('support_phone', $this->support_phone);
        SmsSettings::set('support_email', $this->support_email);

        Notification::make()
            ->title('General settings saved successfully')
            ->success()
            ->send();
    }

    public function updatedCompanyLogo($value): void
    {
        if ($value) {
            $this->validate([
                'company_logo' => 'image|max:2048',
            ]);

            $path = $value->store('logos', 'public');
            SmsSettings::set('company_logo', $path);
            $this->company_logo = $path;

            Notification::make()
                ->title('Logo uploaded successfully')
                ->success()
                ->send();
        }
    }

    public function saveAppearance(): void
    {
        SmsSettings::set('primary_color', $this->primary_color);
        SmsSettings::set('secondary_color', $this->secondary_color);
        SmsSettings::set('font_family', $this->font_family);

        Notification::make()
            ->title('Appearance settings saved successfully')
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