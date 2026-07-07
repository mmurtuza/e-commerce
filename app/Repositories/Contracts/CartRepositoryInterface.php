<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Cart;
use App\Models\CartItem;

interface CartRepositoryInterface
{
    public function getOrCreateForUser(?int $userId, ?string $sessionId): Cart;

    public function addItem(Cart $cart, int $productId, int $quantity, ?int $variantId = null): CartItem;

    public function updateItemQuantity(int $cartItemId, int $quantity): CartItem;

    public function removeItem(int $cartItemId): void;

    public function clear(int $cartId): void;

    public function mergeCarts(Cart $sessionCart, Cart $userCart): Cart;
}
