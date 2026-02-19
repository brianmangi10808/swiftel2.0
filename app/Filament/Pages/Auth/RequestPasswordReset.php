<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function request(): void
    {
        $data = $this->form->getState();

        Log::info('Password reset requested for: ' . $data['email']);

        try {
            $status = Password::sendResetLink(['email' => $data['email']]);
            Log::info('Password reset status: ' . $status);
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }

        parent::request();
    }
}