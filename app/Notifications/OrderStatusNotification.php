<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order $order,
        public readonly OrderStatus $newStatus,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order Update: {$this->order->order_number} — {$this->newStatus->label()}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your order **{$this->order->order_number}** status has been updated.")
            ->line("**New Status:** {$this->newStatus->label()}")
            ->when($this->newStatus === OrderStatus::Shipped, fn ($mail) => $mail->line('Your order is on its way! 🚚')
            )
            ->when($this->newStatus === OrderStatus::Delivered, fn ($mail) => $mail->line('Your order has been delivered. Enjoy your premium computer accessories! 💻')
            )
            ->action('View Order', route('customer.order.show', $this->order->order_number))
            ->salutation(Setting::get('site_name', 'Dinajpur IT Park').' Team 💻');
    }
}
