<?php

declare(strict_types=1);

namespace App\Enums;

enum AddressLabel: string
{
    case Home = 'home';
    case Office = 'office';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Home => 'Home',
            self::Office => 'Office',
            self::Other => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Home => 'success',
            self::Office => 'primary',
            self::Other => 'gray',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }
}
