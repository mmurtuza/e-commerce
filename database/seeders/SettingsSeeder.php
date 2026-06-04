<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate settings first to ensure a clean rebrand
        Setting::truncate();

        $settings = [
            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Dinajpur IT Park', 'type' => SettingType::Text],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Your Ultimate Destination for Premium Computer Accessories', 'type' => SettingType::Text],
            ['group' => 'general', 'key' => 'site_logo', 'value' => 'settings/dinajpur_it_logo.png', 'type' => SettingType::Image],
            ['group' => 'general', 'key' => 'site_favicon', 'value' => null, 'type' => SettingType::Image],
            ['group' => 'general', 'key' => 'free_shipping_threshold', 'value' => '1500', 'type' => SettingType::Number],
            // Appearance
            ['group' => 'appearance', 'key' => 'theme', 'value' => 'default', 'type' => SettingType::Text],
            ['group' => 'appearance', 'key' => 'hero_style', 'value' => 'carousel', 'type' => SettingType::Text],
            // Contact
            ['group' => 'contact', 'key' => 'phone', 'value' => '+880 1712-345678', 'type' => SettingType::Text],
            ['group' => 'contact', 'key' => 'email', 'value' => 'support@dinajpuritpark.com', 'type' => SettingType::Text],
            ['group' => 'contact', 'key' => 'address', 'value' => 'IT Park Complex, Dinajpur - 5200, Bangladesh', 'type' => SettingType::Textarea],
            ['group' => 'contact', 'key' => 'whatsapp', 'value' => '8801712345678', 'type' => SettingType::Text],
            // Social
            ['group' => 'social', 'key' => 'facebook', 'value' => 'https://facebook.com/dinajpuritpark', 'type' => SettingType::Text],
            ['group' => 'social', 'key' => 'instagram', 'value' => 'https://instagram.com/dinajpuritpark', 'type' => SettingType::Text],
            ['group' => 'social', 'key' => 'youtube', 'value' => 'https://youtube.com/dinajpuritpark', 'type' => SettingType::Text],
            // SEO
            ['group' => 'seo', 'key' => 'meta_title', 'value' => 'Dinajpur IT Park - Best Computer & Laptop Accessories Shop in Dinajpur', 'type' => SettingType::Text],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Explore mechanical keyboards, gaming mice, high-speed SSDs, networking routers, and premium computer accessories at Dinajpur IT Park. Quality guaranteed.', 'type' => SettingType::Textarea],
            ['group' => 'seo', 'key' => 'google_analytics_id', 'value' => '', 'type' => SettingType::Text],
            ['group' => 'seo', 'key' => 'facebook_pixel_id', 'value' => '', 'type' => SettingType::Text],
            // Payment
            ['group' => 'payment', 'key' => 'cod_enabled', 'value' => '1', 'type' => SettingType::Boolean],
            ['group' => 'payment', 'key' => 'sslcommerz_enabled', 'value' => '1', 'type' => SettingType::Boolean],
            ['group' => 'payment', 'key' => 'stripe_enabled', 'value' => '0', 'type' => SettingType::Boolean],
            ['group' => 'payment', 'key' => 'bkash_enabled', 'value' => '1', 'type' => SettingType::Boolean],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
