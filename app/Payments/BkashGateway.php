<?php

declare(strict_types=1);

namespace App\Payments;

use App\Contracts\PaymentGateway;
use App\Events\PaymentReceived;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BkashGateway implements PaymentGateway
{
    private bool $enabled;

    private string $appKey;

    private string $appSecret;

    private string $username;

    private string $password;

    private bool $isSandbox;

    public function __construct()
    {
        $this->enabled = (bool) config('payment.gateways.bkash.enabled', false);
        $this->appKey = (string) (config('payment.gateways.bkash.app_key') ?? '');
        $this->appSecret = (string) (config('payment.gateways.bkash.app_secret') ?? '');
        $this->username = (string) (config('payment.gateways.bkash.username') ?? '');
        $this->password = (string) (config('payment.gateways.bkash.password') ?? '');
        $this->isSandbox = (bool) config('payment.gateways.bkash.sandbox', true);
    }

    private function isMock(): bool
    {
        return empty($this->appKey)
            || $this->appKey === 'mock'
            || empty($this->appSecret)
            || $this->appSecret === 'mock';
    }

    private function getApiUrl(string $endpoint): string
    {
        $base = $this->isSandbox
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized';

        return "{$base}/".ltrim($endpoint, '/');
    }

    private function getToken(): string
    {
        return Cache::remember('bkash_token', 3000, function () {
            $response = Http::withHeaders([
                'username' => $this->username,
                'password' => $this->password,
            ])->post($this->getApiUrl('checkout/token/grant'), [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            if ($response->failed() || ! isset($response->json()['id_token'])) {
                throw new \RuntimeException('bKash Authentication failed: '.($response->json()['statusMessage'] ?? 'Unknown error'));
            }

            return $response->json()['id_token'];
        });
    }

    public function initiate(Order $order): array
    {
        if ($this->isMock()) {
            $paymentId = 'MOCK-BKASH-'.strtoupper(Str::random(16));

            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $paymentId,
                'payment_method' => 'bkash',
                'amount' => $order->total,
                'currency' => 'BDT',
                'status' => 'pending',
                'gateway_response' => ['mock' => true],
            ]);

            return ['redirect' => route('payment.bkash.simulator', ['order' => $order->order_number])];
        }

        $token = $this->getToken();

        $callbackUrl = config('payment.gateways.bkash.callback_url');
        if (empty($callbackUrl)) {
            $callbackUrl = route('payment.bkash.callback');
        } elseif (! str_starts_with($callbackUrl, 'http')) {
            $callbackUrl = url($callbackUrl);
        }

        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key' => $this->appKey,
        ])->post($this->getApiUrl('checkout/create'), [
            'mode' => '0011',
            'payerReference' => $order->order_number,
            'callbackURL' => $callbackUrl,
            'amount' => (string) $order->total,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $order->order_number,
        ]);

        $result = $response->json();

        if (isset($result['paymentID']) && isset($result['bkashURL'])) {
            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $result['paymentID'],
                'payment_method' => 'bkash',
                'amount' => $order->total,
                'currency' => 'BDT',
                'status' => 'pending',
                'gateway_response' => $result,
            ]);

            return ['redirect' => $result['bkashURL']];
        }

        throw new \RuntimeException('bKash payment initiation failed: '.($result['statusMessage'] ?? 'Unknown error'));
    }

    public function verify(Request $request): Payment
    {
        $paymentId = $request->get('paymentID');
        if (empty($paymentId)) {
            throw new \InvalidArgumentException('paymentID is missing from bKash callback.');
        }

        $payment = Payment::where('transaction_id', $paymentId)
            ->where('payment_method', 'bkash')
            ->firstOrFail();

        if ($this->isMock()) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'verified' => true,
                    'trxID' => 'TRX'.strtoupper(Str::random(10)),
                ]),
            ]);

            $payment->order->update(['payment_status' => 'paid', 'status' => 'confirmed']);
            event(new PaymentReceived($payment));

            return $payment;
        }

        $token = $this->getToken();

        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key' => $this->appKey,
        ])->post($this->getApiUrl('checkout/execute'), [
            'paymentID' => $paymentId,
        ]);

        $result = $response->json();

        // In tokenized checkout, if successful it returns transaction status Completed or success code
        if (isset($result['transactionStatus']) && $result['transactionStatus'] === 'Completed') {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'gateway_response' => $result,
            ]);

            $payment->order->update(['payment_status' => 'paid', 'status' => 'confirmed']);
            event(new PaymentReceived($payment));

            return $payment;
        }

        throw new \RuntimeException('bKash payment execution failed: '.($result['statusMessage'] ?? 'Unknown error'));
    }

    public function refund(Payment $payment): bool
    {
        $payment->update(['status' => 'refunded']);

        return true;
    }
}
