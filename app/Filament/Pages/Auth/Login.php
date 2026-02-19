<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Notifications\SendOtpNotification;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): LoginResponse|null
    {
        $data = $this->form->getState();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.email' => __('These credentials do not match our records.'),
            ]);
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP
        $user->notify(new SendOtpNotification($otp));

        // Store email in session for OTP verification
        session(['otp_email' => $user->email]);

        // Redirect to OTP verification
        redirect()->to(route('auth.otp.verify'));

        return null;
    }
}