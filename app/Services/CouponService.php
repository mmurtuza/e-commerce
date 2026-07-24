<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;

class CouponService
{
    public function __construct(
        private readonly CouponRepositoryInterface $couponRepository,
    ) {}

    public function validate(string $code, float $subtotal): array
    {
        $coupon = $this->couponRepository->findValidByCode($code);

        if (! $coupon) {
            return ['valid' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return [
                'valid' => false,
                'message' => 'Minimum order amount is ৳'.number_format((float) $coupon->min_order_amount, 2),
            ];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $coupon->calculateDiscount($subtotal),
        ];
    }

    public function incrementUsage(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}
