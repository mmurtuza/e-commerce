<?php

declare(strict_types=1);

namespace App\Enums;

enum PlantType: string
{
    case Indoor = 'indoor';
    case Outdoor = 'outdoor';
    case Both = 'both';
    case NotPlant = 'not_plant';

    public function label(): string
    {
        return match ($this) {
            self::Indoor => 'Indoor',
            self::Outdoor => 'Outdoor',
            self::Both => 'Indoor & Outdoor',
            self::NotPlant => 'Not a Plant',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Indoor => 'success',
            self::Outdoor => 'warning',
            self::Both => 'info',
            self::NotPlant => 'gray',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }
}
