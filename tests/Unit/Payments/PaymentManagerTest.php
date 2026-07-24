<?php

declare(strict_types=1);

namespace Tests\Unit\Payments;

use App\Enums\PaymentMethod;
use App\Payments\BkashGateway;
use App\Payments\CodGateway;
use App\Payments\LemonSqueezyGateway;
use App\Payments\PaddleGateway;
use App\Payments\PaymentManager;
use App\Payments\SslCommerzGateway;
use App\Payments\StripeGateway;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PaymentManagerTest extends TestCase
{
    private CodGateway $cod;

    private SslCommerzGateway $sslcommerz;

    private BkashGateway $bkash;

    private StripeGateway $stripe;

    private PaddleGateway $paddle;

    private LemonSqueezyGateway $lemonsqueezy;

    private PaymentManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cod = $this->createMock(CodGateway::class);
        $this->sslcommerz = $this->createMock(SslCommerzGateway::class);
        $this->bkash = $this->createMock(BkashGateway::class);
        $this->stripe = $this->createMock(StripeGateway::class);
        $this->paddle = $this->createMock(PaddleGateway::class);
        $this->lemonsqueezy = $this->createMock(LemonSqueezyGateway::class);

        $this->manager = new PaymentManager(
            $this->cod,
            $this->sslcommerz,
            $this->bkash,
            $this->stripe,
            $this->paddle,
            $this->lemonsqueezy
        );
    }

    public function test_driver_resolves_all_supported_gateways(): void
    {
        $this->assertSame($this->cod, $this->manager->driver(PaymentMethod::Cod->value));
        $this->assertSame($this->sslcommerz, $this->manager->driver(PaymentMethod::SslCommerz->value));
        $this->assertSame($this->bkash, $this->manager->driver(PaymentMethod::Bkash->value));
        $this->assertSame($this->stripe, $this->manager->driver(PaymentMethod::Stripe->value));
        $this->assertSame($this->paddle, $this->manager->driver(PaymentMethod::Paddle->value));
        $this->assertSame($this->lemonsqueezy, $this->manager->driver(PaymentMethod::LemonSqueezy->value));
    }

    public function test_driver_throws_exception_for_unsupported_gateway(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment gateway [unsupported] is not supported.');

        $this->manager->driver('unsupported');
    }
}
