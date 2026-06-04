<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Events\PaymentReceived;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeController extends Controller
{
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('payment.gateways.stripe.webhook_secret');

        if (empty($endpointSecret)) {
            Log::error('Stripe webhook secret is not set.');
            return response()->json(['error' => 'Webhook secret not set'], 500);
        }

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $payment = Payment::where('transaction_id', $session->id)->first();

            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'gateway_response' => array_merge($payment->gateway_response ?? [], $session->toArray()),
                ]);

                $payment->order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed'
                ]);

                event(new PaymentReceived($payment));
            }
        }

        return response()->json(['status' => 'success']);
    }
}
