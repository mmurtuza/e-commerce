<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    private array $categories = [
        ['en' => 'Keyboards', 'bn' => 'কিবোর্ড', 'icon' => '⌨️', 'children' => [
            ['en' => 'Mechanical Keyboards', 'bn' => 'মেকানিক্যাল কিবোর্ড'],
            ['en' => 'Wireless Keyboards', 'bn' => 'ওয়্যারলেস কিবোর্ড'],
            ['en' => 'Membrane Keyboards', 'bn' => 'মেমব্রেন কিবোর্ড'],
        ]],
        ['en' => 'Mice', 'bn' => 'মাউস', 'icon' => '🖱️', 'children' => [
            ['en' => 'Gaming Mice', 'bn' => 'গেমিং মাউস'],
            ['en' => 'Wireless Mice', 'bn' => 'ওয়্যারলেস মাউস'],
            ['en' => 'Office Mice', 'bn' => 'অফিস মাউস'],
        ]],
        ['en' => 'Headphones & Audio', 'bn' => 'হেডফোন ও অডিও', 'icon' => '🎧', 'children' => [
            ['en' => 'Gaming Headsets', 'bn' => 'গেমিং হেডসেট'],
            ['en' => 'Bluetooth Earphones', 'bn' => 'ব্লুটুথ ইয়ারফোন'],
            ['en' => 'Desktop Speakers', 'bn' => 'ডেস্কটপ স্পিকার'],
        ]],
        ['en' => 'Storage Devices', 'bn' => 'স্টোরেজ ডিভাইস', 'icon' => '💾', 'children' => [
            ['en' => 'Solid State Drives (SSDs)', 'bn' => 'এসএসডি (SSD)'],
            ['en' => 'USB Pendrives', 'bn' => 'ইউএসবি পেনড্রাইভ'],
            ['en' => 'External Hard Drives', 'bn' => 'এক্সটার্নাল হার্ড ড্রাইভ'],
        ]],
        ['en' => 'PC Components', 'bn' => 'পিসি কম্পোনেন্ট', 'icon' => '🔌', 'children' => [
            ['en' => 'DDR4 & DDR5 RAM', 'bn' => 'র‍্যাম (RAM)'],
            ['en' => 'Power Supplies', 'bn' => 'পাওয়ার সাপ্লাই'],
            ['en' => 'Cooling Fans', 'bn' => 'কুলিং ফ্যান'],
        ]],
        ['en' => 'Laptop Accessories', 'bn' => 'ল্যাপটপ অ্যাক্সেসরিজ', 'icon' => '💻', 'children' => [
            ['en' => 'Laptop Stands', 'bn' => 'ল্যাপটপ স্ট্যান্ড'],
            ['en' => 'Laptop Bags & Sleeves', 'bn' => 'ল্যাপটপ ব্যাগ'],
            ['en' => 'Power Chargers', 'bn' => 'পাওয়ার চার্জার'],
        ]],
        ['en' => 'Networking Accessories', 'bn' => 'নেটওয়ার্কিং অ্যাক্সেসরিজ', 'icon' => '🌐', 'children' => [
            ['en' => 'Wi-Fi Routers', 'bn' => 'ওয়াই-ফাই রাউটার'],
            ['en' => 'Ethernet LAN Cables', 'bn' => 'ল্যান ক্যাবল'],
            ['en' => 'USB Wi-Fi Adapters', 'bn' => 'ওয়াই-ফাই অ্যাডাপ্টার'],
        ]],
    ];

    public function run(): void
    {
        Category::truncate();

        foreach ($this->categories as $index => $cat) {
            $parent = Category::create([
                'slug' => Str::slug($cat['en']),
                'icon' => $cat['icon'] ?? '🔌',
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);

            $parent->translations()->createMany([
                ['locale' => 'en', 'name' => $cat['en'], 'description' => $cat['en'].' and computing devices.'],
                ['locale' => 'bn', 'name' => $cat['bn'], 'description' => $cat['bn'].' এবং কম্পিউটার সামগ্রী।'],
            ]);

            foreach ($cat['children'] as $childIndex => $child) {
                $childCat = Category::create([
                    'parent_id' => $parent->id,
                    'slug' => Str::slug($child['en']),
                    'sort_order' => $childIndex + 1,
                    'is_active' => true,
                ]);

                $childCat->translations()->createMany([
                    ['locale' => 'en', 'name' => $child['en']],
                    ['locale' => 'bn', 'name' => $child['bn']],
                ]);
            }
        }
    }
}
