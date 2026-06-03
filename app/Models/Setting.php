<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SettingType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    protected function casts(): array
    {
        return [
            'type' => SettingType::class,
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        static $memoryCache = [];

        if (array_key_exists($key, $memoryCache)) {
            return $memoryCache[$key];
        }

        return $memoryCache[$key] = Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->getCastedValue() : $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value !== null ? (string) $value : null]);
        } else {
            $metadata = static::getMetadataForKey($key);
            static::create([
                'key' => $key,
                'value' => $value !== null ? (string) $value : null,
                'group' => $metadata['group'],
                'type' => $metadata['type'],
            ]);
        }

        Cache::forget("setting:{$key}");
    }

    protected static function getMetadataForKey(string $key): array
    {
        $map = [
            'site_name' => ['group' => 'general', 'type' => SettingType::Text],
            'site_tagline' => ['group' => 'general', 'type' => SettingType::Text],
            'site_logo' => ['group' => 'general', 'type' => SettingType::Image],
            'site_favicon' => ['group' => 'general', 'type' => SettingType::Image],
            'free_shipping_threshold' => ['group' => 'general', 'type' => SettingType::Number],
            'theme' => ['group' => 'appearance', 'type' => SettingType::Text],
            'phone' => ['group' => 'contact', 'type' => SettingType::Text],
            'email' => ['group' => 'contact', 'type' => SettingType::Text],
            'address' => ['group' => 'contact', 'type' => SettingType::Textarea],
            'whatsapp' => ['group' => 'contact', 'type' => SettingType::Text],
            'facebook' => ['group' => 'social', 'type' => SettingType::Text],
            'instagram' => ['group' => 'social', 'type' => SettingType::Text],
            'youtube' => ['group' => 'social', 'type' => SettingType::Text],
            'meta_title' => ['group' => 'seo', 'type' => SettingType::Text],
            'meta_description' => ['group' => 'seo', 'type' => SettingType::Textarea],
            'google_analytics_id' => ['group' => 'seo', 'type' => SettingType::Text],
            'facebook_pixel_id' => ['group' => 'seo', 'type' => SettingType::Text],
            'cod_enabled' => ['group' => 'payment', 'type' => SettingType::Boolean],
            'sslcommerz_enabled' => ['group' => 'payment', 'type' => SettingType::Boolean],
            'stripe_enabled' => ['group' => 'payment', 'type' => SettingType::Boolean],
        ];

        return $map[$key] ?? ['group' => 'general', 'type' => SettingType::Text];
    }


    public function getCastedValue(): mixed
    {
        return match($this->type) {
            SettingType::Boolean => (bool) $this->value,
            SettingType::Number => (float) $this->value,
            SettingType::Json => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
