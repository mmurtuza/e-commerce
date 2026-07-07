<?php

declare(strict_types=1);

namespace App\Enums;

enum BannerType: string
{
    case HeroSlider = 'hero_slider';
    case Popup = 'popup';
    case Promotional = 'promotional';

    public function label(): string
    {
        return match ($this) {
            self::HeroSlider => 'Hero Slider',
            self::Popup => 'Popup Banner',
            self::Promotional => 'Promotional Banner',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HeroSlider => 'primary',
            self::Popup => 'warning',
            self::Promotional => 'success',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }
}
