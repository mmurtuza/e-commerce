<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function initiate(Order $order): array;

    public function verify(Request $request): Payment;

    public function refund(Payment $payment): bool;
}
