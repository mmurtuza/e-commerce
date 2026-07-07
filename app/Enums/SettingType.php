<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Number = 'number';
    case Boolean = 'boolean';
    case Json = 'json';
    case Image = 'image';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Textarea => 'Textarea',
            self::Number => 'Number',
            self::Boolean => 'Boolean',
            self::Json => 'JSON',
            self::Image => 'Image',
        };
    }

    public function color(): string
    {
        return 'gray';
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->toArray();
    }
}
