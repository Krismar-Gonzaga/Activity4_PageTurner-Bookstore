<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;

class NewDeviceLoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ip;
    public $userAgent;
    public $timestamp;

    public function __construct($ip, $userAgent)
    {
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->timestamp = now();
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $agent = new Agent();
        $agent->setUserAgent($this->userAgent);
        
        $device = $agent->device() ?: 'Unknown Device';
        $platform = $agent->platform() ?: 'Unknown OS';
        $browser = $agent->browser() ?: 'Unknown Browser';

        return (new MailMessage)
            ->subject('New Device Login - PageTurner')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('We detected a new login to your account from an unrecognized device.')
            ->line('**Device Information:**')
            ->line('- Device: ' . $device)
            ->line('- Operating System: ' . $platform)
            ->line('- Browser: ' . $browser)
            ->line('- IP Address: ' . $this->ip)
            ->line('- Time: ' . $this->timestamp->format('F j, Y, g:i a'))
            ->line('If this was you, you can ignore this alert.')
            ->line('If you do not recognize this activity, please secure your account immediately.')
            ->action('Review Account Security', route('profile.edit'))
            ->salutation('Stay Vigilant! 👀');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Device Login Detected',
            'message' => 'A new login was detected from IP: ' . $this->ip,
            'type' => 'security',
            'time' => $this->timestamp->toDateTimeString(),
        ];
    }
}