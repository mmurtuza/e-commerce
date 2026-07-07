<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing products to start fresh
        Product::truncate();

        // Retrieve computer accessory categories
        $mechKb = Category::where('slug', 'mechanical-keyboards')->first() ?? Category::first();
        $gamingMouse = Category::where('slug', 'gaming-mice')->first() ?? Category::first();
        $gamingHeadsets = Category::where('slug', 'gaming-headsets')->first() ?? Category::first();
        $ssds = Category::where('slug', 'solid-state-drives-ssds')->first() ?? Category::first();
        $ram = Category::where('slug', 'ddr4-ddr5-ram')->first() ?? Category::first();
        $laptopStands = Category::where('slug', 'laptop-stands')->first() ?? Category::first();
        $speakers = Category::where('slug', 'desktop-speakers')->first() ?? Category::first();
        $routers = Category::where('slug', 'wi-fi-routers')->first() ?? Category::first();

        $products = [
            [
                'name_en' => 'Fantech MaxFit61 Mechanical Keyboard',
                'name_bn' => 'ফ্যানটেক ম্যাক্সফিট৬১ মেকানিক্যাল কিবোর্ড',
                'price' => 3800,
                'category' => $mechKb,
                'image' => 'products/keyboard.png',
                'sku' => 'DIT-KB-0001',
                'desc_en' => 'A compact 60% layout mechanical keyboard with hot-swappable switches and custom RGB lighting.',
                'desc_bn' => 'হট-সোয়াপবল সুইচ এবং কাস্টম আরজিবি লাইটিং সহ একটি কমপ্যাক্ট ৬০% লেআউট মেকানিক্যাল কিবোর্ড।',
            ],
            [
                'name_en' => 'Redragon K552 Kumara Rainbow Keyboard',
                'name_bn' => 'রেড্রাগন কে৫৫২ কুমারা রেইনবো কিবোর্ড',
                'price' => 3200,
                'category' => $mechKb,
                'image' => 'products/keyboard.png',
                'sku' => 'DIT-KB-0002',
                'desc_en' => 'Tenkeyless mechanical gaming keyboard with tactile blue switches and metallic construction.',
                'desc_bn' => 'ট্যাকটাইল ব্লু সুইচ এবং মেটালিক বডি সহ টেনকিল্যাস মেকানিক্যাল গেমিং কিবোর্ড।',
            ],
            [
                'name_en' => 'Razer DeathAdder Essential Gaming Mouse',
                'name_bn' => 'রেজার ডেথঅ্যাডার এসেনশিয়াল গেমিং মাউস',
                'price' => 1850,
                'category' => $gamingMouse,
                'image' => 'products/mouse.png',
                'sku' => 'DIT-MS-0001',
                'desc_en' => 'Ergonomic gaming mouse with a 6400 DPI optical sensor and 5 programmable Hyperesponse buttons.',
                'desc_bn' => '৬৪০০ ডিপিআই অপটিক্যাল সেন্সর এবং ৫টি প্রোগ্রাবেল বাটন সহ এর্গোনমিক গেমিং মাউস।',
            ],
            [
                'name_en' => 'Logitech G102 Lightsync Gaming Mouse',
                'name_bn' => 'লজিটেক জি১০২ লাইটসিঙ্ক গেমিং মাউস',
                'price' => 2100,
                'category' => $gamingMouse,
                'image' => 'products/mouse.png',
                'sku' => 'DIT-MS-0002',
                'desc_en' => 'Classic design with an 8000 DPI gaming-grade sensor and customizable Lightsync RGB color waves.',
                'desc_bn' => '৮০০০ ডিপিআই গেমিং-গ্রেড সেন্সর এবং কাস্টমাইজযোগ্য আরজিবি লাইটসিঙ্ক সহ ক্লাসিক গেমিং মাউস।',
            ],
            [
                'name_en' => 'Logitech G435 Lightspeed Gaming Headset',
                'name_bn' => 'লজিটেক জি৪৩৫ লাইটস্পিড গেমিং হেডসেট',
                'price' => 6800,
                'category' => $gamingHeadsets,
                'image' => 'products/headphones.png',
                'sku' => 'DIT-AD-0001',
                'desc_en' => 'Ultra-lightweight wireless headset with low-latency Bluetooth and built-in beamforming microphones.',
                'desc_bn' => 'লো-লেটেন্সি ব্লুটুথ এবং বিল্ট-ইন বিমফর্মিং মাইক্রোফোন সহ অতি-হালকা ওয়্যারলেস হেডসেট।',
            ],
            [
                'name_en' => 'Samsung 980 Pro 1TB NVMe M.2 SSD',
                'name_bn' => 'স্যামসাং ৯৮০ প্রো ১টিবি এনভিএমই এম.২ এসএসডি',
                'price' => 11500,
                'category' => $ssds,
                'image' => 'products/ssd.png',
                'sku' => 'DIT-ST-0001',
                'desc_en' => 'Next-level PCIe Gen4 SSD reaching read speeds up to 7000 MB/s for extreme gaming and content creation.',
                'desc_bn' => 'গেমিং এবং ভারী কাজের জন্য ৭০০০ এমবি/সেকেন্ড রিড স্পিড সম্পন্ন চমৎকার পিসিআইই জেন৪ এসএসডি।',
            ],
            [
                'name_en' => 'TP-Link Archer C6 AC1200 Wi-Fi Router',
                'name_bn' => 'টিপি-লিংক আর্চার সি৬ এসি১২০০ ওয়াই-ফাই রাউটার',
                'price' => 2850,
                'category' => $routers,
                'image' => 'products/router.png',
                'sku' => 'DIT-NW-0001',
                'desc_en' => 'Dual-band MU-MIMO Gigabit Wi-Fi router with 4 external antennas providing smooth wireless coverage.',
                'desc_bn' => 'মসৃণ ওয়্যারলেস কভারেজের জন্য ৪টি এক্সটার্নাল অ্যান্টেনা সহ ডুয়াল-ব্যান্ড গিগাবিট ওয়াই-ফাই রাউটার।',
            ],
            [
                'name_en' => 'Corsair Vengeance LPX 16GB DDR4 RAM',
                'name_bn' => 'করসেয়ার ভেঞ্জেন্স এলপিএক্স ১৬জিবি ডিডিআর৪ র‍্যাম',
                'price' => 4500,
                'category' => $ram,
                'image' => 'products/ssd.png',
                'sku' => 'DIT-CP-0001',
                'desc_en' => 'High-performance DDR4 RAM module clocked at 3200MHz, designed for Intel motherboard overclocking.',
                'desc_bn' => 'ইন্টেল মাদারবোর্ড ওভারক্লকিংয়ের জন্য ডিজাইন করা ৩২-শহী ১০০ মেগাহার্টজ ডিডিআর৪ র‍্যাম।',
            ],
            [
                'name_en' => 'Aluminum Ergonomic Laptop Stand',
                'name_bn' => 'অ্যালুমিনিয়াম এর্গোনমিক ল্যাপটপ স্ট্যান্ড',
                'price' => 1250,
                'category' => $laptopStands,
                'image' => 'products/keyboard.png',
                'sku' => 'DIT-LA-0001',
                'desc_en' => 'Adjustable aluminum laptop stand offering optimum heat dissipation and improved typing posture.',
                'desc_bn' => 'সর্বোত্তম তাপ নিষ্কাশন এবং উন্নত টাইপিং ভঙ্গি নিশ্চিত করতে সামঞ্জস্যযোগ্য ল্যাপটপ স্ট্যান্ড।',
            ],
            [
                'name_en' => 'Fantech GS201 RGB Desktop Speakers',
                'name_bn' => 'ফ্যানটেক জিএস২০১ আরজিবি ডেস্কটপ স্পিকার',
                'price' => 950,
                'category' => $speakers,
                'image' => 'products/headphones.png',
                'sku' => 'DIT-AD-0002',
                'desc_en' => 'Compact USB-powered desk speakers with modern breathing RGB lighting effects and clear high-resolution audio.',
                'desc_bn' => 'আধুনিক আরজিবি লাইটিং ইফেক্ট এবং উচ্চ রেজোলিউশনের অডিও সহ ইউএসবি স্পিকার।',
            ],
        ];

        foreach ($products as $index => $data) {
            $isFeatured = $index < 6;
            $isNew = $index >= 4;

            $product = Product::create([
                'category_id' => $data['category']?->id ?? 1,
                'slug' => Str::slug($data['name_en']),
                'sku' => $data['sku'],
                'price' => $data['price'],
                'compare_price' => rand(0, 1) ? $data['price'] * 1.15 : null,
                'stock_quantity' => rand(10, 100),
                'low_stock_threshold' => 5,
                'is_active' => true,
                'is_featured' => $isFeatured,
                'is_new_arrival' => $isNew,
                'requires_shipping' => true,
                'plant_type' => null,
                'sunlight' => null,
                'watering' => null,
                'difficulty' => null,
            ]);

            $product->images()->create([
                'path' => $data['image'],
                'alt_text' => $data['name_en'],
                'is_primary' => true,
                'sort_order' => 1,
            ]);

            $product->translations()->createMany([
                [
                    'locale' => 'en',
                    'name' => $data['name_en'],
                    'short_description' => $data['desc_en'],
                    'description' => "<p>{$data['desc_en']}</p><h4>Specifications</h4><ul><li>Brand Official Product</li><li>Quality Guaranteed</li><li>Excellent Ergonomics & Durability</li></ul>",
                    'care_instructions' => 'Keep clean and dry. Avoid spills. Plug carefully.',
                    'meta_title' => "Buy {$data['name_en']} Online | Dinajpur IT Park",
                    'meta_description' => "Get your hands on {$data['name_en']} at the best rates in Bangladesh. Order now from Dinajpur IT Park.",
                ],
                [
                    'locale' => 'bn',
                    'name' => $data['name_bn'],
                    'short_description' => $data['desc_bn'],
                    'description' => "<p>{$data['desc_bn']}</p><h4>স্পেসিফিকেশন</h4><ul><li>অফিসিয়াল প্রোডাক্ট</li><li>গুনগত মান সম্পন্ন</li><li>চমৎকার এর্গোনমিক্স ও স্থায়িত্ব</li></ul>",
                    'care_instructions' => 'পরিষ্কার ও শুষ্ক রাখুন। তরল পদার্থ থেকে দূরে রাখুন। সাবধানে প্লাগ করুন।',
                ],
            ]);
        }
    }
}
