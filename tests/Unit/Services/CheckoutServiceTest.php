<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Events\OrderPlaced;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\User;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\CheckoutService;
use App\Services\CouponService;
use App\Services\ShippingService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartRepositoryInterface&MockObject $cartRepository;

    private ProductRepositoryInterface&MockObject $productRepository;

    private ShippingService $shippingService;

    private CouponService&MockObject $couponService;

    private CheckoutService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = $this->createMock(CartRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->shippingService = new ShippingService;
        $this->couponService = $this->createMock(CouponService::class);

        $this->service = new CheckoutService(
            $this->cartRepository,
            $this->productRepository,
            $this->shippingService,
            $this->couponService
        );
    }

    private function createProductWithTranslation(int $stock = 10, float $price = 100.0): Product
    {
        $category = Category::create([
            'slug' => 'cat-'.uniqid(),
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'slug' => 'product-'.uniqid(),
            'sku' => 'SKU-'.uniqid(),
            'price' => $price,
            'stock_quantity' => $stock,
            'is_active' => true,
        ]);

        ProductTranslation::create([
            'product_id' => $product->id,
            'locale' => 'en',
            'name' => 'Sample Product',
        ]);

        return $product;
    }

    public function test_place_order_throws_exception_when_stock_is_insufficient(): void
    {
        $product = $this->createProductWithTranslation(stock: 1);

        $cartItem = new CartItem([
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => 100.0,
        ]);
        $cartItem->setRelation('product', $product);

        $cart = new Cart;
        $cart->setRelation('items', new Collection([$cartItem]));

        $user = User::factory()->create();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Insufficient stock for: Sample Product');

        $this->service->placeOrder($user, $cart, ['division' => 'Dhaka'], 'cod');
    }

    public function test_place_order_creates_order_and_triggers_event(): void
    {
        Event::fake([OrderPlaced::class]);

        $product = $this->createProductWithTranslation(stock: 10, price: 500.0);

        $cartItem = new CartItem([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 500.0,
        ]);
        $cartItem->setRelation('product', $product);

        $cart = new Cart;
        $cart->setRelation('items', new Collection([$cartItem]));

        $user = User::factory()->create();

        $this->productRepository->expects($this->once())
            ->method('updateStock')
            ->with($product->id, 2, true);

        $order = $this->service->placeOrder(
            $user,
            $cart,
            ['division' => 'Dhaka', 'address' => '123 Main St'],
            'cod'
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($user->id, $order->user_id);
        $this->assertEquals(1000.0, (float) $order->subtotal);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Event::assertDispatched(OrderPlaced::class);
    }

    public function test_rollback_order_restores_stock_and_deletes_order(): void
    {
        $user = User::factory()->create();
        $product = $this->createProductWithTranslation();

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'subtotal' => 200.0,
            'discount_amount' => 0,
            'shipping_amount' => 60.0,
            'tax_amount' => 0,
            'total' => 260.0,
            'shipping_address' => ['division' => 'Dhaka'],
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Sample Product',
            'product_sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 100.0,
            'total_price' => 200.0,
        ]);

        $this->productRepository->expects($this->once())
            ->method('updateStock')
            ->with($product->id, 2, false);

        $this->service->rollbackOrder($order);

        $this->assertSoftDeleted($order);
    }
}
