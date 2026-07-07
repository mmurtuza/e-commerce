<?php

declare(strict_types=1);

namespace App\Enums;

enum WateringFrequency: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Biweekly = 'biweekly';
    case Monthly = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Daily',
            self::Weekly => 'Weekly',
            self::Biweekly => 'Every Two Weeks',
            self::Monthly => 'Monthly',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Daily => 'info',
            self::Weekly => 'success',
            self::Biweekly => 'warning',
            self::Monthly => 'gray',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }
}
