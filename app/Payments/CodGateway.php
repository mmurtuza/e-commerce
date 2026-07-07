<?php

declare(strict_types=1);

namespace App\Payments;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CodGateway implements PaymentGateway
{
    public function initiate(Order $order): array
    {
        Payment::create([
            'order_id' => $order->id,
            'transaction_id' => 'COD-'.strtoupper(Str::random(10)),
            'payment_method' => 'cod',
            'amount' => $order->total,
            'currency' => 'BDT',
            'status' => 'pending',
        ]);

        return [
            'redirect' => route('checkout.success', $order->order_number),
        ];
    }

    public function verify(Request $request): Payment
    {
        $order = Order::where('order_number', $request->get('order_number'))->firstOrFail();

        return $order->payment;
    }

    public function refund(Payment $payment): bool
    {
        $payment->update(['status' => 'refunded']);

        return true;
    }
}
