<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $statusMessages = [
            'pending' => 'Your order is being processed.',
            'processing' => 'Your order is being prepared.',
            'shipped' => 'Your order has been shipped!',
            'delivered' => 'Your order has been delivered.',
            'cancelled' => 'Your order has been cancelled.',
            'refunded' => 'Your order has been refunded.',
        ];

        $message = $statusMessages[$this->newStatus] ?? 'Your order status has been updated.';

        return (new MailMessage)
            ->subject('Order Status Update #' . $this->order->order_number . ' - PageTurner')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your order #' . $this->order->order_number . ' status has been updated.')
            ->line('**Status Update:**')
            ->line('From: **' . ucfirst($this->oldStatus) . '**')
            ->line('To: **' . ucfirst($this->newStatus) . '**')
            ->line('')
            ->line($message)
            ->action('Track Your Order', route('orders.show', $this->order->id))
            ->salutation('Thank you for choosing PageTurner!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Order Status Updated #' . $this->order->order_number,
            'message' => 'Your order status changed from ' . $this->oldStatus . ' to ' . $this->newStatus,
            'type' => 'order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'time' => now()->toDateTimeString(),
        ];
    }
}