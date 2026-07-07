<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getActive(array $filters, int $perPage = 16): LengthAwarePaginator;

    public function getFeatured(int $limit = 8): Collection;

    public function getNewArrivals(int $limit = 8): Collection;

    public function getByCategory(int $categoryId, array $filters, int $perPage = 16): LengthAwarePaginator;

    public function getRelated(Product $product, int $limit = 4): Collection;

    public function findBySlug(string $slug): ?Product;

    public function updateStock(int $productId, int $quantity, bool $decrement = true): void;

    public function getLowStock(): Collection;
}
