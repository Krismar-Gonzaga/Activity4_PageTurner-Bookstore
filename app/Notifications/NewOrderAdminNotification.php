<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $customer;

    public function __construct(Order $order, User $customer)
    {
        $this->order = $order;
        $this->customer = $customer;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('New Order Received #' . $this->order->order_number . ' - PageTurner')
            ->greeting('Hello Admin!')
            ->line('A new order has been placed on your store.')
            ->line('**Order Details:**')
            ->line('- Order Number: #' . $this->order->order_number)
            ->line('- Customer: ' . $this->customer->name)
            ->line('- Customer Email: ' . $this->customer->email)
            ->line('- Order Date: ' . $this->order->created_at->format('F j, Y, g:i a'))
            ->line('- Total Amount: $' . number_format($this->order->total_amount, 2))
            ->line('- Payment Method: ' . ucfirst(str_replace('_', ' ', $this->order->payment_method)))
            ->line('')
            ->line('**Order Items:**');

        foreach ($this->order->items as $item) {
            $mailMessage->line('- ' . $item->book->title . ' x' . $item->quantity . ' - $' . number_format($item->price * $item->quantity, 2));
        }

        return $mailMessage
            ->action('View Order Details', route('orders.show', $this->order->id))
            ->salutation('PageTurner Store');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Order #' . $this->order->order_number,
            'message' => 'New order from ' . $this->customer->name . ' - $' . number_format($this->order->total_amount, 2),
            'type' => 'admin_order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->customer->name,
            'amount' => $this->order->total_amount,
            'action_url' => route('orders.show', $this->order->id),
            'time' => now()->toDateTimeString(),
        ];
    }
}