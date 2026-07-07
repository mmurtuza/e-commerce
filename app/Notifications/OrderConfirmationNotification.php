<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order $order,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order Confirmed: {$this->order->order_number} | ".Setting::get('site_name', 'Dinajpur IT Park'))
            ->greeting("Hello {$notifiable->name}! 💻")
            ->line("Your order **{$this->order->order_number}** has been confirmed.")
            ->line('**Order Total:** ৳'.number_format($this->order->total, 2))
            ->line("**Payment Method:** {$this->order->payment_method->label()}")
            ->action('Track Your Order', route('customer.order.show', $this->order->order_number))
            ->line('Thank you for shopping at '.Setting::get('site_name', 'Dinajpur IT Park').'! Your items will be carefully packed and delivered.')
            ->salutation('Happy Tech Setup! 🔌 — '.Setting::get('site_name', 'Dinajpur IT Park').' Team');
    }
}
