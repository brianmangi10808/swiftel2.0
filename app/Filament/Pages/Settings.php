<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\SystemSetting;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $title = 'System Settings';
    protected static string $view = 'filament.pages.settings';
    protected static string $panel = 'admin';
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        // Load saved values
        $this->form->fill([
            'pppoe_expiry_reminder_times' => SystemSetting::get('pppoe_expiry_reminder_times', []),


        'prune_after_expiry' =>
            \App\Models\SystemSetting::get('prune_after_expiry', '30_days'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('PPPoE Expiry Reminder Settings')
                    ->description('Choose when customers should receive expiry reminders.')
                    ->schema([
                        Forms\Components\Select::make('pppoe_expiry_reminder_times')
                            ->label('Notification Times')
                            ->multiple()
                            ->options([
                                '7_days_before'   => '7 days before',
                                '5_days_before'   => '5 days before',
                                '4_days_before'   => '4 days before',
                                '3_days_before'   => '3 days before',
                                '2_days_before'   => '2 days before',
                                '1_day_before'    => '1 day before',
                                '12_hours_before' => '12 hours before',
                                '6_hours_before'  => '6 hours before',
                                '4_hours_before'  => '4 hours before',
                            ])
                            ->searchable()
                            ->placeholder('Select reminder times...')
                            ->columnSpanFull(),
                    ]),


                    Forms\Components\Section::make('Customer Pruning Settings')
    ->description('Automatically disable or prune customers after expiry.')
    ->schema([
        Forms\Components\Select::make('prune_after_expiry')
            ->label('Prune Customer After Expiry')
            ->options([
                '7_days'   => '7 Days',
                '14_days'  => '14 Days',
                '30_days'  => '30 Days',
                '60_days'  => '60 Days',
                '90_days'  => '90 Days',
                '180_days' => '180 Days',
                '1_year'   => '1 Year',
            ])
            ->searchable()
            ->required()
            ->placeholder('Select pruning duration...'),
    ]),

            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SystemSetting::set('pppoe_expiry_reminder_times', $data['pppoe_expiry_reminder_times']);
        SystemSetting::set('prune_after_expiry', $data['prune_after_expiry']);

        Notification::make()
            ->title('Settings saved successfully.')
            ->success()
            ->send();
    }
}
