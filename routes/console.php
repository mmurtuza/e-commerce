<?php

declare(strict_types=1);

use App\Jobs\CleanExpiredCarts;
use App\Jobs\GenerateSitemap;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Clean expired guest carts daily at 2am
Schedule::job(new CleanExpiredCarts)->dailyAt('02:00');

// Regenerate sitemap daily at 3am
Schedule::job(new GenerateSitemap)->dailyAt('03:00');

// Clear expired coupons daily at midnight
Schedule::call(function () {
    Coupon::where('expires_at', '<', now())->update(['is_active' => false]);
})->dailyAt('00:00')->name('clear-expired-coupons');

// Send low stock alerts daily at 8am
Schedule::call(function () {
    Product::lowStock()->get()->each(function ($product) {
        Log::warning("Low stock: {$product->name} has {$product->stock_quantity} remaining.");
    });
})->dailyAt('08:00')->name('low-stock-alerts');
