<?php

declare(strict_types=1);

namespace App\Payments;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaddleGateway implements PaymentGateway
{
    private bool $enabled;
    private string $vendorId;
    private string $authCode;
    private bool $isSandbox;
    private string $apiUrl;

    public function __construct()
    {
        $this->enabled = (bool) config('payment.gateways.paddle.enabled', false);
        $this->vendorId = (string) (config('payment.gateways.paddle.vendor_id') ?? '');
        $this->authCode = (string) (config('payment.gateways.paddle.auth_code') ?? '');
        $this->isSandbox = (bool) config('payment.gateways.paddle.sandbox', true);
        
        // Paddle Billing (v2) API URLs
        $this->apiUrl = $this->isSandbox 
            ? 'https://sandbox-api.paddle.com' 
            : 'https://api.paddle.com';
    }

    public function initiate(Order $order): array
    {
        if (empty($this->authCode)) {
            throw new \RuntimeException('Paddle auth code is not configured.');
        }

        // Create a transaction in Paddle Billing
        $response = Http::withToken($this->authCode)
            ->post("{$this->apiUrl}/transactions", [
                'items' => [
                    [
                        'price' => [
                            'description' => 'Order #' . $order->order_number,
                            'unit_price' => [
                                'amount' => (int) round($order->total * 100), // Minor units
                                'currency_code' => 'USD', // Paddle generally requires standard currencies. Ensure USD is ok or map BDT to USD. Paddle does not support BDT natively. We'll use USD for demonstration.
                            ],
                            'product' => [
                                'name' => 'Order from ' . config('app.name'),
                                'tax_category' => 'standard',
                            ],
                        ],
                        'quantity' => 1,
                    ]
                ],
                'custom_data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);

        if ($response->failed()) {
            \Illuminate\Support\Facades\Log::error('Paddle API Error', ['response' => $response->json()]);
            throw new \RuntimeException('Paddle payment initiation failed: ' . $response->body());
        }

        $transaction = $response->json('data');
        $checkoutUrl = $transaction['checkout']['url'] ?? '';

        if (empty($checkoutUrl)) {
            throw new \RuntimeException('Paddle payment initiation failed: Checkout URL not returned.');
        }

        Payment::create([
            'order_id' => $order->id,
            'transaction_id' => $transaction['id'],
            'payment_method' => 'paddle',
            'amount' => $order->total,
            'currency' => 'USD', // Paddle fallback
            'status' => 'pending',
            'gateway_response' => $transaction,
        ]);

        return ['redirect' => $checkoutUrl];
    }

    public function verify(Request $request): Payment
    {
        // Verified via webhook
        throw new \RuntimeException('Paddle verification should be handled via webhook.');
    }

    public function refund(Payment $payment): bool
    {
        // Implement refund logic via Paddle API if needed.
        return false;
    }
}
