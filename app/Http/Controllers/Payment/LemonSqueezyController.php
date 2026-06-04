<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Events\PaymentReceived;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LemonSqueezyController extends Controller
{
    public function webhook(Request $request)
    {
        $secret = config('payment.gateways.lemonsqueezy.webhook_secret');
        
        if (empty($secret)) {
            Log::error('Lemon Squeezy webhook secret is not set.');
            return response()->json(['error' => 'Webhook secret not set'], 500);
        }

        $payload = $request->getContent();
        $signature = $request->header('X-Signature');

        $hash = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($hash, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);
        $eventName = $data['meta']['event_name'] ?? '';

        if ($eventName === 'order_created') {
            $checkoutId = $data['data']['attributes']['checkout_id'] ?? null;
            
            // Note: Lemon Squeezy order_created webhook doesn't strictly pass the checkout ID sometimes, 
            // but we can pass custom data in the checkout payload and it returns in the webhook.
            $customData = $data['meta']['custom_data'] ?? [];
            $orderId = $customData['order_id'] ?? null;

            if ($orderId) {
                // Find payment by order ID and pending status (since checkout ID might not match directly depending on the object)
                $payment = Payment::where('order_id', $orderId)
                    ->where('payment_method', 'lemonsqueezy')
                    ->where('status', 'pending')
                    ->first();

                if ($payment) {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                        'gateway_response' => array_merge($payment->gateway_response ?? [], $data),
                    ]);

                    $payment->order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed'
                    ]);

                    event(new PaymentReceived($payment));
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
