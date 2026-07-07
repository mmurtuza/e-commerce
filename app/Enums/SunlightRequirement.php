<?php

declare(strict_types=1);

namespace App\Enums;

enum SunlightRequirement: string
{
    case FullSun = 'full_sun';
    case PartialShade = 'partial_shade';
    case FullShade = 'full_shade';
    case Any = 'any';

    public function label(): string
    {
        return match ($this) {
            self::FullSun => 'Full Sun',
            self::PartialShade => 'Partial Shade',
            self::FullShade => 'Full Shade',
            self::Any => 'Any Light',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FullSun => 'warning',
            self::PartialShade => 'info',
            self::FullShade => 'gray',
            self::Any => 'success',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }
}
