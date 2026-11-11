<?php

namespace App\Notifications;

use App\Models\Dashboard\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaidOrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'price' => $this->order->total_price,
            'shipping_price' => $this->order->shipping_price,
            'total_price' => $this->order->total_price + $this->order->shipping_price,
            'message' => __('orders.order_paid', ['id' => $this->order->id]),
            'created_at' => now()->toDateTimeString(),
        ];
    }
    

    public function databaseType(object $notifiable): string
    {
        return 'paid_order';
    }

    public function broadcastType(): string
    {
        return 'paid_order';
    }
}
