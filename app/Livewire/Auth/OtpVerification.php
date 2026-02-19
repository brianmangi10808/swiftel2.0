<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Notifications\SendOtpNotification;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class OtpVerification extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public string $email = '';

    public function mount(): void
    {
        $this->email = session('otp_email', '');

        if (empty($this->email)) {
            redirect()->route('filament.admin.auth.login');
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('otp')
                    ->label('Enter OTP')
                    ->required()
                    ->maxLength(6)
                    ->placeholder('Enter 6-digit OTP')
                    ->helperText('Check your email for the OTP code'),
            ])
            ->statePath('data');
    }

    public function verify(): void
    {
        $data = $this->form->getState();
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            Notification::make()
                ->title('Invalid session. Please login again.')
                ->danger()
                ->send();
            redirect()->route('filament.admin.auth.login');
            return;
        }

        if ($user->otp !== $data['otp'] || now()->isAfter($user->otp_expires_at)) {
            Notification::make()
                ->title('Invalid or expired OTP. Please try again.')
                ->danger()
                ->send();
            return;
        }

        // Clear OTP
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        // Login user
        auth()->login($user);
        session()->forget('otp_email');

        redirect()->intended(route('filament.admin.pages.dashboard'));
    }

    public function resendOtp(): void
    {
        $user = User::where('email', $this->email)->first();

        if ($user) {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);
            $user->notify(new SendOtpNotification($otp));

            Notification::make()
                ->title('OTP resent successfully!')
                ->success()
                ->send();
        }
    }

 public function render(): \Illuminate\View\View
{
    return view('livewire.auth.otp-verification');
}
}