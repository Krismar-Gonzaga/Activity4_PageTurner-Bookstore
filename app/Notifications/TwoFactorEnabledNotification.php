<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorEnabledNotification extends Notification  implements ShouldQueue
{
    use Queueable;

    public $method;
    /**
     * Create a new notification instance.
     */
    public function __construct($method)
    {
        $this->method = $method;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $methodText = $this->method === 'app' ? 'Authenticator App' : 'Email OTP';
        
        return (new MailMessage)
            ->subject('Two-Factor Authentication Enabled - PageTurner')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Two-factor authentication has been successfully enabled on your account using **' . $methodText . '**.')
            ->line('Your account is now more secure!')
            ->line('Remember to store your recovery codes in a safe place.')
            ->action('View Recovery Codes', route('profile.two-factor.recovery-codes'))
            ->line('If you did not enable 2FA, please secure your account immediately.')
            ->salutation('Stay Safe! 🔒');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Two-Factor Authentication Enabled',
            'message' => '2FA has been enabled on your account using ' . ($this->method === 'app' ? 'Authenticator App' : 'Email OTP'),
            'type' => 'security',
            'action_url' => route('profile.two-factor.recovery-codes'),
            'time' => now()->toDateTimeString(),
        ];
    }
}
