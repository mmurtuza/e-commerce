<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\CouponType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\User;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Services\CartService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartRepositoryInterface&MockObject $cartRepository;

    private CouponRepositoryInterface&MockObject $couponRepository;

    private CartService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = $this->createMock(CartRepositoryInterface::class);
        $this->couponRepository = $this->createMock(CouponRepositoryInterface::class);
        $this->service = new CartService($this->cartRepository, $this->couponRepository);
    }

    public function test_get_cart_delegates_to_repository(): void
    {
        $cart = new Cart;
        $this->cartRepository->expects($this->once())
            ->method('getOrCreateForUser')
            ->willReturn($cart);

        $result = $this->service->getCart();
        $this->assertSame($cart, $result);
    }

    public function test_add_item_delegates_to_repository(): void
    {
        $cart = new Cart;
        $cart->id = 1;
        $cartItem = new CartItem;

        $this->cartRepository->method('getOrCreateForUser')->willReturn($cart);
        $this->cartRepository->expects($this->once())
            ->method('addItem')
            ->with($cart, 10, 2, 5)
            ->willReturn($cartItem);

        $result = $this->service->addItem(10, 2, 5);
        $this->assertSame($cartItem, $result);
    }

    public function test_update_item_delegates_to_repository(): void
    {
        $cartItem = new CartItem;
        $this->cartRepository->expects($this->once())
            ->method('updateItemQuantity')
            ->with(1, 3)
            ->willReturn($cartItem);

        $result = $this->service->updateItem(1, 3);
        $this->assertSame($cartItem, $result);
    }

    public function test_remove_item_delegates_to_repository(): void
    {
        $this->cartRepository->expects($this->once())
            ->method('removeItem')
            ->with(1);

        $this->service->removeItem(1);
    }

    public function test_apply_coupon_returns_error_when_coupon_invalid(): void
    {
        $this->couponRepository->expects($this->once())
            ->method('findValidByCode')
            ->with('INVALID')
            ->willReturn(null);

        $result = $this->service->applyCoupon('INVALID');

        $this->assertFalse($result['success']);
        $this->assertSame('Invalid or expired coupon code.', $result['message']);
    }

    public function test_apply_coupon_returns_error_when_subtotal_is_insufficient(): void
    {
        $coupon = new Coupon([
            'code' => 'MIN1000',
            'min_order_amount' => 1000.0,
            'type' => CouponType::Fixed,
            'value' => 100.0,
        ]);

        $cartMock = $this->createPartialMock(Cart::class, ['update']);
        $cartMock->setRelation('items', new Collection([new CartItem(['unit_price' => 500.0, 'quantity' => 1])]));

        $this->couponRepository->method('findValidByCode')->willReturn($coupon);
        $this->cartRepository->method('getOrCreateForUser')->willReturn($cartMock);

        $result = $this->service->applyCoupon('MIN1000');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Minimum order amount of ৳', $result['message']);
    }

    public function test_apply_coupon_success(): void
    {
        $coupon = new Coupon;
        $coupon->id = 42;
        $coupon->code = 'SAVE10';
        $coupon->type = CouponType::Fixed;
        $coupon->value = 50.0;

        $cartMock = $this->createPartialMock(Cart::class, ['update']);
        $cartMock->setRelation('items', new Collection([new CartItem(['unit_price' => 500.0, 'quantity' => 1])]));
        $cartMock->expects($this->once())
            ->method('update')
            ->with(['coupon_id' => 42]);

        $this->couponRepository->method('findValidByCode')->willReturn($coupon);
        $this->cartRepository->method('getOrCreateForUser')->willReturn($cartMock);

        $result = $this->service->applyCoupon('SAVE10');

        $this->assertTrue($result['success']);
        $this->assertEquals(50.0, $result['discount']);
    }

    public function test_remove_coupon(): void
    {
        $cartMock = $this->createPartialMock(Cart::class, ['update']);
        $cartMock->expects($this->once())
            ->method('update')
            ->with(['coupon_id' => null]);

        $this->cartRepository->method('getOrCreateForUser')->willReturn($cartMock);

        $this->service->removeCoupon();
    }

    public function test_get_item_count(): void
    {
        $cart = new Cart;
        $cart->setRelation('items', new Collection([new CartItem(['quantity' => 7])]));
        $this->cartRepository->method('getOrCreateForUser')->willReturn($cart);

        $this->assertSame(7, $this->service->getItemCount());
    }

    public function test_clear(): void
    {
        $cart = new Cart;
        $cart->id = 99;
        $this->cartRepository->method('getOrCreateForUser')->willReturn($cart);
        $this->cartRepository->expects($this->once())
            ->method('clear')
            ->with(99);

        $this->service->clear();
    }

    public function test_merge_guest_cart_does_nothing_when_not_authenticated(): void
    {
        $this->cartRepository->expects($this->never())->method('getOrCreateForUser');

        $this->service->mergeGuestCart();
    }

    public function test_merge_guest_cart_merges_when_authenticated_and_session_cart_has_items(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $sessionCart = new Cart;
        $sessionCart->id = 1;
        $sessionCart->setRelation('items', new Collection([new CartItem]));

        $userCart = new Cart;
        $userCart->id = 2;

        $this->cartRepository->expects($this->exactly(2))
            ->method('getOrCreateForUser')
            ->willReturnOnConsecutiveCalls($sessionCart, $userCart);

        $this->cartRepository->expects($this->once())
            ->method('mergeCarts')
            ->with($sessionCart, $userCart);

        $this->service->mergeGuestCart();
    }
}
