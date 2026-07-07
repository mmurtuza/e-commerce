<?php

declare(strict_types=1);

namespace App\Enums;

enum CouponType: string
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixed Amount (৳)',
            self::Percentage => 'Percentage (%)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Fixed => 'success',
            self::Percentage => 'info',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }
}
