<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case Cod = 'cod';
    case SslCommerz = 'sslcommerz';
    case Bkash = 'bkash';
    case Stripe = 'stripe';
    case Paddle = 'paddle';
    case LemonSqueezy = 'lemonsqueezy';

    public function label(): string
    {
        return match ($this) {
            self::Cod => 'Cash on Delivery',
            self::SslCommerz => 'SSLCommerz',
            self::Bkash => 'bKash',
            self::Stripe => 'Stripe',
            self::Paddle => 'Paddle',
            self::LemonSqueezy => 'Lemon Squeezy',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Cod => 'gray',
            self::SslCommerz => 'primary',
            self::Bkash => 'danger',
            self::Stripe => 'info',
            self::Paddle => 'warning',
            self::LemonSqueezy => 'success',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }
}
