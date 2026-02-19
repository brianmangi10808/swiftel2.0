<?php

namespace App\Providers;

use App\Models\SmsGateway;
use App\Observers\SmsGatewayObserver;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        SmsGateway::observe(SmsGatewayObserver::class);

        // Fix SSL certificate mismatch
        $this->configureMailer();

        // Fix password reset URL to use Filament route
        \Illuminate\Auth\Notifications\ResetPassword::createUrlUsing(
            function ($notifiable, $token) {
                return route('filament.admin.auth.password-reset.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]);
            }
        );
    }

    protected function configureMailer(): void
    {
        $config = config('mail.mailers.smtp');

        $transport = new EsmtpTransport(
            $config['host'],
            (int) $config['port'],
            false
        );

        $transport->setUsername($config['username'] ?? '');
        $transport->setPassword($config['password'] ?? '');

        /** @var SocketStream $stream */
        $stream = $transport->getStream();
        $stream->setStreamOptions([
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);

        // Directly set the transport on the mailer
        app('mailer')->setSymfonyTransport($transport);
    }
}