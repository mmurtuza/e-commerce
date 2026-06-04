<?php

declare(strict_types=1);

namespace App\Payments;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LemonSqueezyGateway implements PaymentGateway
{
    private bool $enabled;
    private string $apiKey;
    private string $storeId;
    private string $apiUrl = 'https://api.lemonsqueezy.com/v1';

    public function __construct()
    {
        $this->enabled = (bool) config('payment.gateways.lemonsqueezy.enabled', false);
        $this->apiKey = (string) (config('payment.gateways.lemonsqueezy.api_key') ?? '');
        $this->storeId = (string) (config('payment.gateways.lemonsqueezy.store_id') ?? '');
    }

    public function initiate(Order $order): array
    {
        if (empty($this->apiKey) || empty($this->storeId)) {
            throw new \RuntimeException('Lemon Squeezy API Key or Store ID is not configured.');
        }

        $response = Http::withToken($this->apiKey)
            ->accept('application/vnd.api+json')
            ->contentType('application/vnd.api+json')
            ->post("{$this->apiUrl}/checkouts", [
                'data' => [
                    'type' => 'checkouts',
                    'attributes' => [
                        'checkout_data' => [
                            'custom' => [
                                'order_id' => $order->id,
                                'order_number' => $order->order_number,
                            ],
                        ],
                        'custom_price' => (int) round($order->total * 100), // Minor units
                        'product_options' => [
                            'name' => 'Order #' . $order->order_number,
                            'description' => 'Payment for order from ' . config('app.name'),
                            'redirect_url' => route('checkout.success', ['orderNumber' => $order->order_number]),
                        ]
                    ],
                    'relationships' => [
                        'store' => [
                            'data' => [
                                'type' => 'stores',
                                'id' => (string) $this->storeId,
                            ]
                        ],
                        'variant' => [
                            'data' => [
                                'type' => 'variants',
                                'id' => '1', // You typically need a generic custom variant ID here or omit it if your store settings allow pure custom prices without variants. Lemon Squeezy usually requires a generic variant for custom prices. For this example, we assume variant ID '1' is configured as a generic "Custom Amount" variant.
                            ]
                        ]
                    ]
                ]
            ]);

        if ($response->failed()) {
            \Illuminate\Support\Facades\Log::error('Lemon Squeezy API Error', ['response' => $response->json()]);
            throw new \RuntimeException('Lemon Squeezy payment initiation failed: ' . $response->body());
        }

        $checkout = $response->json('data');
        $checkoutUrl = $checkout['attributes']['url'] ?? '';

        if (empty($checkoutUrl)) {
            throw new \RuntimeException('Lemon Squeezy payment initiation failed: Checkout URL not returned.');
        }

        Payment::create([
            'order_id' => $order->id,
            'transaction_id' => $checkout['id'],
            'payment_method' => 'lemonsqueezy',
            'amount' => $order->total,
            'currency' => 'USD', // Adjust according to your Lemon Squeezy store settings
            'status' => 'pending',
            'gateway_response' => $checkout,
        ]);

        return ['redirect' => $checkoutUrl];
    }

    public function verify(Request $request): Payment
    {
        throw new \RuntimeException('Lemon Squeezy verification should be handled via webhook.');
    }

    public function refund(Payment $payment): bool
    {
        return false;
    }
}
