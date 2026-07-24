<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Services\CouponService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CouponServiceTest extends TestCase
{
    private CouponRepositoryInterface&MockObject $repository;

    private CouponService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(CouponRepositoryInterface::class);
        $this->service = new CouponService($this->repository);
    }

    public function test_validate_returns_invalid_when_coupon_not_found(): void
    {
        $this->repository->expects($this->once())
            ->method('findValidByCode')
            ->with('INVALID')
            ->willReturn(null);

        $result = $this->service->validate('INVALID', 100.0);

        $this->assertFalse($result['valid']);
        $this->assertSame('Invalid or expired coupon code.', $result['message']);
    }

    public function test_validate_returns_invalid_when_subtotal_is_below_minimum_order_amount(): void
    {
        $coupon = new Coupon([
            'code' => 'MIN500',
            'min_order_amount' => 500.0,
            'type' => CouponType::Fixed,
            'value' => 50.0,
        ]);

        $this->repository->expects($this->once())
            ->method('findValidByCode')
            ->with('MIN500')
            ->willReturn($coupon);

        $result = $this->service->validate('MIN500', 300.0);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Minimum order amount is ৳500.00', $result['message']);
    }

    public function test_validate_returns_valid_result_with_calculated_discount(): void
    {
        $coupon = new Coupon([
            'code' => 'SAVE10',
            'min_order_amount' => 100.0,
            'type' => CouponType::Percentage,
            'value' => 10.0,
        ]);

        $this->repository->expects($this->once())
            ->method('findValidByCode')
            ->with('SAVE10')
            ->willReturn($coupon);

        $result = $this->service->validate('SAVE10', 200.0);

        $this->assertTrue($result['valid']);
        $this->assertSame($coupon, $result['coupon']);
        $this->assertEquals(20.0, $result['discount']);
    }

    public function test_increment_usage_calls_increment_on_coupon(): void
    {
        /** @var Coupon&MockObject $couponMock */
        $couponMock = $this->createPartialMock(Coupon::class, ['increment']);
        $couponMock->expects($this->once())
            ->method('increment')
            ->with('used_count');

        $this->service->incrementUsage($couponMock);
    }
}
