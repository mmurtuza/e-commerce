<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function getOrCreateForUser(?int $userId, ?string $sessionId): Cart
    {
        if ($userId) {
            return Cart::with('items.product')->firstOrCreate(['user_id' => $userId]);
        }

        return Cart::with('items.product')->firstOrCreate(['session_id' => $sessionId]);
    }

    public function addItem(Cart $cart, int $productId, int $quantity, ?int $variantId = null): CartItem
    {
        $product = Product::findOrFail($productId);
        $price = $product->price;

        if ($variantId) {
            $variant = $product->variants()->findOrFail($variantId);
            $price = $product->price + $variant->price_modifier;
        }

        $existing = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);

            return $existing->fresh();
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'unit_price' => $price,
        ]);
    }

    public function updateItemQuantity(int $cartItemId, int $quantity): CartItem
    {
        $item = CartItem::findOrFail($cartItemId);
        $item->update(['quantity' => $quantity]);

        return $item;
    }

    public function removeItem(int $cartItemId): void
    {
        CartItem::findOrFail($cartItemId)->delete();
    }

    public function clear(int $cartId): void
    {
        CartItem::where('cart_id', $cartId)->delete();
    }

    public function mergeCarts(Cart $sessionCart, Cart $userCart): Cart
    {
        foreach ($sessionCart->items as $item) {
            $this->addItem($userCart, $item->product_id, $item->quantity, $item->variant_id);
        }

        $sessionCart->delete();

        return $userCart->load('items.product');
    }
}
