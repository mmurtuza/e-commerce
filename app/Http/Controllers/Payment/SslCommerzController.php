<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Payments\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SslCommerzController
{
    public function __construct(
        private readonly PaymentManager $paymentManager,
    ) {}

    public function success(Request $request): RedirectResponse
    {
        try {
            $payment = $this->paymentManager->driver('sslcommerz')->verify($request);

            return redirect()->route('checkout.success', $payment->order->order_number)
                ->with('success', 'Payment successful!');
        } catch (\Exception $e) {
            return redirect()->route('checkout.index')->with('error', 'Payment verification failed: '.$e->getMessage());
        }
    }

    public function fail(Request $request): RedirectResponse
    {
        $payment = Payment::where('transaction_id', $request->get('tran_id'))->first();

        if ($payment) {
            $payment->update([
                'status' => PaymentStatus::Failed,
                'gateway_response' => $request->all(),
            ]);
        }

        return redirect()->route('checkout.index')->with('error', 'Payment failed. Please try again.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        return redirect()->route('checkout.index')->with('warning', 'Payment was cancelled.');
    }

    public function ipn(Request $request): void
    {
        try {
            $this->paymentManager->driver('sslcommerz')->verify($request);
        } catch (\Exception $e) {
            // Silently capture IPN verification failures or log
        }
    }
}
