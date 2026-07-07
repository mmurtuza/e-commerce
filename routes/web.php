<?php

declare(strict_types=1);

use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LanguageController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\ShopController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Payment\BkashController;
use App\Http\Controllers\Payment\LemonSqueezyController;
use App\Http\Controllers\Payment\PaddleController;
use App\Http\Controllers\Payment\SslCommerzController;
use App\Http\Controllers\Payment\StripeController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

// Language switcher
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Shop
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/{slug}', [ShopController::class, 'show'])->name('show');
});

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'addItem'])->name('add');
    Route::patch('/item/{item}', [CartController::class, 'updateItem'])->name('update');
    Route::delete('/item/{item}', [CartController::class, 'removeItem'])->name('remove');
    Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('/coupon', [CartController::class, 'removeCoupon'])->name('coupon.remove');
});

// Checkout
Route::prefix('checkout')->name('checkout.')->middleware(['auth', 'verified', 'checkout.guard'])->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
});
// Checkout success — outside checkout.guard so it works after cart is cleared
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])
    ->name('checkout.success')
    ->middleware('auth');

// Wishlist
Route::prefix('wishlist')->name('wishlist.')->middleware('auth')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
});

// Reviews
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');

// Blog
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Static pages
Route::get('/about', [PageController::class, 'about'])->name('page.about');
Route::get('/contact', [PageController::class, 'contact'])->name('page.contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('page.contact.submit');
Route::get('/faq', [PageController::class, 'faq'])->name('page.faq');
Route::get('/terms', [PageController::class, 'terms'])->name('page.terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('page.privacy');
Route::get('/return-policy', [PageController::class, 'returnPolicy'])->name('page.return-policy');

// Customer Dashboard
Route::prefix('account')->name('customer.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderNumber}', [CustomerController::class, 'orderShow'])->name('order.show');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::get('/addresses', [CustomerController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [CustomerController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{address}', [CustomerController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{address}', [CustomerController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [CustomerController::class, 'changePassword'])->name('password.update');
});

// Cart item count (AJAX)
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// External payment callbacks & webhooks (No CSRF)
Route::withoutMiddleware([PreventRequestForgery::class])->group(function () {
    // SSLCommerz
    Route::post('/payment/sslcommerz/success', [SslCommerzController::class, 'success'])->name('payment.sslcommerz.success');
    Route::post('/payment/sslcommerz/fail', [SslCommerzController::class, 'fail'])->name('payment.sslcommerz.fail');
    Route::post('/payment/sslcommerz/cancel', [SslCommerzController::class, 'cancel'])->name('payment.sslcommerz.cancel');
    Route::post('/payment/sslcommerz/ipn', [SslCommerzController::class, 'ipn'])->name('payment.sslcommerz.ipn');

    // Stripe Webhook
    Route::post('/payment/stripe/webhook', [StripeController::class, 'webhook'])->name('payment.stripe.webhook');

    // Paddle Webhook
    Route::post('/payment/paddle/webhook', [PaddleController::class, 'webhook'])->name('payment.paddle.webhook');

    // Lemon Squeezy Webhook
    Route::post('/payment/lemonsqueezy/webhook', [LemonSqueezyController::class, 'webhook'])->name('payment.lemonsqueezy.webhook');
});

// bKash Checkout routes
Route::get('/payment/bkash/callback', [BkashController::class, 'callback'])->name('payment.bkash.callback');
Route::get('/payment/bkash/simulator/{order}', [BkashController::class, 'simulator'])->name('payment.bkash.simulator');

require __DIR__.'/auth.php';
