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
    public ?string $payment_method = 'paybill'; // paybill or till
    public ?string $paybill = null;
    public ?string $till_number = null;
    public ?string $phone_number = null;
    public ?string $support_number = null;
    public ?string $support_email = null;
    public ?string $primary_color = '#f59e0b'; // Default amber color
    public ?string $secondary_color = '#6b7280'; // Default gray color
    public ?string $font_family = 'Inter'; // Default font

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
        $this->payment_method = SmsSettings::get('payment_method', 'paybill');
        $this->paybill = SmsSettings::get('paybill');
        $this->till_number = SmsSettings::get('till_number');
        $this->phone_number = SmsSettings::get('phone_number');
        $this->support_number = SmsSettings::get('support_number');
        $this->support_email = SmsSettings::get('support_email');
        $this->primary_color = SmsSettings::get('primary_color', '#f59e0b');
        $this->secondary_color = SmsSettings::get('secondary_color', '#6b7280');
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
        // Validate the input
        $this->validate([
            'company_name' => 'nullable|string|max:255',
            'system_email' => 'nullable|email|max:255',
            'payment_method' => 'required|in:paybill,till',
            'paybill' => 'nullable|string|max:50',
            'till_number' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:20',
            'support_number' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:255',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:100',
        ]);

        SmsSettings::set('company_name', $this->company_name);
        SmsSettings::set('system_email', $this->system_email);
        SmsSettings::set('payment_method', $this->payment_method);
        SmsSettings::set('paybill', $this->paybill);
        SmsSettings::set('till_number', $this->till_number);
        SmsSettings::set('phone_number', $this->phone_number);
        SmsSettings::set('support_number', $this->support_number);
        SmsSettings::set('support_email', $this->support_email);
        SmsSettings::set('primary_color', $this->primary_color);
        SmsSettings::set('secondary_color', $this->secondary_color);
        SmsSettings::set('font_family', $this->font_family);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    public function updatedCompanyLogo($value): void
    {
        if ($value) {
            // Store the uploaded file
            $path = $value->store('logos', 'public');

            // Delete old logo if exists
            $oldLogo = SmsSettings::get('company_logo');
            if ($oldLogo && \Storage::disk('public')->exists($oldLogo)) {
                \Storage::disk('public')->delete($oldLogo);
            }

            // Save new logo path
            SmsSettings::set('company_logo', $path);
            $this->company_logo = $path;

            Notification::make()
                ->title('Logo uploaded successfully')
                ->success()
                ->send();
        }
    }

    public function deleteLogo(): void
    {
        $logo = SmsSettings::get('company_logo');

        if ($logo && \Storage::disk('public')->exists($logo)) {
            \Storage::disk('public')->delete($logo);
        }

        SmsSettings::set('company_logo', null);
        $this->company_logo = null;

        Notification::make()
            ->title('Logo deleted successfully')
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