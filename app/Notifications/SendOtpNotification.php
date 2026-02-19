<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $otp
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Swiftel OTP Code')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your One-Time Password (OTP) for login verification is:')
            ->line('**' . $this->otp . '**')
            ->line('This OTP is valid for **10 minutes** only.')
            ->line('If you did not request this, please ignore this email.')
            ->salutation('Regards, Swiftel Support Team');
    }
}