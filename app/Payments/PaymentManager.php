<?php

declare(strict_types=1);

namespace App\Payments;

use App\Contracts\PaymentGateway;
use App\Enums\PaymentMethod;
use InvalidArgumentException;

class PaymentManager
{
    private array $gateways = [];

    public function __construct(
        private readonly CodGateway $cod,
        private readonly SslCommerzGateway $sslcommerz,
        private readonly BkashGateway $bkash,
    ) {
        $this->gateways = [
            PaymentMethod::Cod->value => $this->cod,
            PaymentMethod::SslCommerz->value => $this->sslcommerz,
            PaymentMethod::Bkash->value => $this->bkash,
        ];
    }

    public function driver(string $method): PaymentGateway
    {
        if (!isset($this->gateways[$method])) {
            throw new InvalidArgumentException("Payment gateway [{$method}] is not supported.");
        }

        return $this->gateways[$method];
    }
}
