<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BkashController
{
    public function __construct(
        private readonly PaymentManager $paymentManager,
    ) {}

    public function callback(Request $request): RedirectResponse
    {
        $status = $request->get('status');
        $paymentId = $request->get('paymentID');

        if (empty($paymentId)) {
            return redirect()->route('checkout.index')->with('error', 'bKash payment ID missing.');
        }

        $payment = Payment::where('transaction_id', $paymentId)
            ->where('payment_method', 'bkash')
            ->first();

        if (! $payment) {
            return redirect()->route('checkout.index')->with('error', 'bKash payment record not found.');
        }

        if ($status === 'success') {
            try {
                $payment = $this->paymentManager->driver('bkash')->verify($request);

                return redirect()->route('checkout.success', $payment->order->order_number)
                    ->with('success', 'Payment via bKash completed successfully!');
            } catch (\Exception $e) {
                return redirect()->route('checkout.index')
                    ->with('error', 'bKash payment verification failed: '.$e->getMessage());
            }
        }

        if ($status === 'cancel') {
            $payment->update(['status' => 'cancelled']);

            return redirect()->route('checkout.index')->with('warning', 'bKash payment was cancelled.');
        }

        $payment->update(['status' => 'failed']);

        return redirect()->route('checkout.index')->with('error', 'bKash payment failed. Please try again.');
    }

    public function simulator(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $payment = Payment::where('order_id', $order->id)
            ->where('payment_method', 'bkash')
            ->where('status', 'pending')
            ->firstOrFail();

        return view('payment.bkash.simulator', compact('order', 'payment'));
    }
}
