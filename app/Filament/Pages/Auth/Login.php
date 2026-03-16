<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Notifications\SendOtpNotification;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class Login extends BaseLogin
{
    public function authenticate(): LoginResponse|null
    {
        $data = $this->form->getState();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.email' => __('These credentials do not match our records.'),
            ]);
        }

        // Generate device fingerprint
        $deviceFingerprint = md5(request()->userAgent() . request()->ip());

        // Check if device was verified within last 30 days
        $isDeviceVerified = $user->device_fingerprint === $deviceFingerprint 
            && $user->device_verified_at 
            && $user->device_verified_at->isAfter(now()->subDays(30));

        if ($isDeviceVerified) {
            // Device is trusted - login directly without OTP
            auth()->login($user, $data['remember'] ?? false);
            session()->regenerate();

            return app(LoginResponse::class);
        }

        // First time on this device - verify and remember for 30 days
        $user->update([
            'device_verified_at' => now(),
            'device_fingerprint' => $deviceFingerprint,
        ]);

        // Login without OTP
        auth()->login($user, $data['remember'] ?? false);
        session()->regenerate();

        return app(LoginResponse::class);
    }
}