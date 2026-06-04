<?php

declare(strict_types=1);

namespace App\Payments;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeGateway implements PaymentGateway
{
    private bool $enabled;
    private string $key;
    private string $secret;
    private string $webhookSecret;

    public function __construct()
    {
        $this->enabled = (bool) config('payment.gateways.stripe.enabled', false);
        $this->key = (string) (config('payment.gateways.stripe.key') ?? '');
        $this->secret = (string) (config('payment.gateways.stripe.secret') ?? '');
        $this->webhookSecret = (string) (config('payment.gateways.stripe.webhook_secret') ?? '');

        if (!empty($this->secret)) {
            Stripe::setApiKey($this->secret);
        }
    }

    public function initiate(Order $order): array
    {
        if (empty($this->secret)) {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'bdt', // Or USD depending on your Stripe account setup
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    // Stripe requires amount in smallest currency unit (e.g., paisa/cents)
                    'unit_amount' => (int) round($item->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        // Add shipping fee as a separate line item if present
        if ($order->shipping_fee > 0) {
             $lineItems[] = [
                'price_data' => [
                    'currency' => 'bdt',
                    'product_data' => [
                        'name' => 'Shipping Fee',
                    ],
                    'unit_amount' => (int) round($order->shipping_fee * 100),
                ],
                'quantity' => 1,
            ];
        }
        
        // Add discount as a negative line item if present (Requires custom setup or coupon in Stripe, simpler to adjust unit amounts or add a negative line item if Stripe allows it. Actually, Stripe doesn't allow negative line items directly. It's better to pass discounts via Stripe Coupons or just pass the total amount as a single line item if calculating per-item is complex with discounts).
        // For simplicity and to match the exact order total, let's just create a single line item for the whole order total if there's a discount, or we keep it simple. Let's just use the order total as one line item to avoid rounding/discount mismatch issues.

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'bdt',
                    'product_data' => [
                        'name' => 'Order #' . $order->order_number,
                        'description' => 'Payment for order from ' . config('app.name'),
                    ],
                    'unit_amount' => (int) round($order->total * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success', ['orderNumber' => $order->order_number]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.index'),
            'client_reference_id' => (string) $order->id,
            'metadata' => [
                'order_number' => $order->order_number,
            ],
        ]);

        Payment::create([
            'order_id' => $order->id,
            'transaction_id' => $session->id,
            'payment_method' => 'stripe',
            'amount' => $order->total,
            'currency' => 'BDT',
            'status' => 'pending',
            'gateway_response' => $session->toArray(),
        ]);

        return ['redirect' => $session->url];
    }

    public function verify(Request $request): Payment
    {
        // For Stripe, verification is usually done via Webhooks to be secure.
        // But we can also retrieve the session from the session_id if needed on the success page.
        // This method will be primarily used by the webhook controller.
        
        throw new \RuntimeException('Stripe verification should be handled via webhook.');
    }

    public function refund(Payment $payment): bool
    {
        if (empty($this->secret)) {
             return false;
        }

        try {
            $refund = \Stripe\Refund::create([
                'payment_intent' => $payment->gateway_response['payment_intent'] ?? null,
                'amount' => (int) round($payment->amount * 100),
            ]);

            if ($refund->status === 'succeeded') {
                $payment->update(['status' => 'refunded']);
                return true;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Stripe refund failed: ' . $e->getMessage());
        }

        return false;
    }
}
