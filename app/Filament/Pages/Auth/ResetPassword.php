<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\ResetPassword as BaseResetPassword;

class ResetPassword extends BaseResetPassword
{
    public function mount(?string $email = null, ?string $token = null): void
    {
        $this->token = request()->query('token') ?? $token;
        $emailValue = request()->query('email') ?? $email;

        $this->form->fill([
            'email' => $emailValue,
            'token' => $this->token,
        ]);
    }
}