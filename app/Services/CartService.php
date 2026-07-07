<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly CouponRepositoryInterface $couponRepository,
    ) {}

    public function getCart(): Cart
    {
        return $this->cartRepository->getOrCreateForUser(
            Auth::id(),
            Session::getId()
        );
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $cart = $this->getCart();

        return $this->cartRepository->addItem($cart, $productId, $quantity, $variantId);
    }

    public function updateItem(int $cartItemId, int $quantity): CartItem
    {
        return $this->cartRepository->updateItemQuantity($cartItemId, $quantity);
    }

    public function removeItem(int $cartItemId): void
    {
        $this->cartRepository->removeItem($cartItemId);
    }

    public function applyCoupon(string $code): array
    {
        $coupon = $this->couponRepository->findValidByCode($code);

        if (! $coupon) {
            return ['success' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        $cart = $this->getCart();

        if ($coupon->min_order_amount && $cart->subtotal < $coupon->min_order_amount) {
            return [
                'success' => false,
                'message' => "Minimum order amount of ৳{$coupon->min_order_amount} required.",
            ];
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return [
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => $coupon->calculateDiscount($cart->subtotal),
        ];
    }

    public function removeCoupon(): void
    {
        $this->getCart()->update(['coupon_id' => null]);
    }

    public function getItemCount(): int
    {
        return $this->getCart()->item_count;
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        $this->cartRepository->clear($cart->id);
    }

    public function mergeGuestCart(): void
    {
        if (! Auth::check()) {
            return;
        }

        $sessionCart = $this->cartRepository->getOrCreateForUser(null, Session::getId());
        if ($sessionCart->items->isEmpty()) {
            return;
        }

        $userCart = $this->cartRepository->getOrCreateForUser(Auth::id(), null);
        $this->cartRepository->mergeCarts($sessionCart, $userCart);
    }
}
