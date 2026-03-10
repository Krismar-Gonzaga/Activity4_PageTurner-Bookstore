<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Confirmation #' . $this->order->order_number . ' - PageTurner')
            ->greeting('Thank you for your order, ' . $notifiable->name . '!')
            ->line('Your order has been successfully placed.')
            ->line('**Order Details:**')
            ->line('- Order Number: #' . $this->order->order_number)
            ->line('- Order Date: ' . $this->order->created_at->format('F j, Y, g:i a'))
            ->line('- Total Amount: $' . number_format($this->order->total_amount, 2))
            ->line('- Payment Method: ' . ucfirst(str_replace('_', ' ', $this->order->payment_method)))
            ->line('- Order Status: ' . ucfirst($this->order->status))
            ->line('You will receive updates when your order status changes.')
            ->action('View Order Details', route('orders.show', $this->order->id))
            ->line('Thank you for shopping with PageTurner!')
            ->salutation('Happy Reading! 📚');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Order Confirmed #' . $this->order->order_number,
            'message' => 'Your order has been placed successfully. Total: $' . number_format($this->order->total_amount, 2),
            'type' => 'order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total_amount,
            'action_url' => route('orders.show', $this->order->id),
            'time' => now()->toDateTimeString(),
        ];
    }
}