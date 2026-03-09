<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorDisabledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Two-Factor Authentication Disabled - PageTurner')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Two-factor authentication has been disabled on your account.')
            ->line('Your account is now less secure. We recommend re-enabling 2FA.')
            ->action('Enable 2FA', route('profile.two-factor'))
            ->line('If you did not disable 2FA, please secure your account immediately.')
            ->salutation('Stay Safe! 🔒');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Two-Factor Authentication Disabled',
            'message' => '2FA has been disabled on your account',
            'type' => 'security',
            'time' => now()->toDateTimeString(),
        ];
    }
}