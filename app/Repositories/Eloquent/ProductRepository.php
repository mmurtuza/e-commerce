<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getActive(array $filters = [], int $perPage = 16): LengthAwarePaginator
    {
        $query = Product::with(['translations', 'images', 'category'])
            ->active();

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function getFeatured(int $limit = 8): Collection
    {
        return Product::with(['translations', 'images', 'category.translations'])
            ->withAvg(['reviews' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->active()
            ->featured()
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getNewArrivals(int $limit = 8): Collection
    {
        return Product::with(['translations', 'images', 'category.translations'])
            ->withAvg(['reviews' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->active()
            ->newArrivals()
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getByCategory(int $categoryId, array $filters = [], int $perPage = 16): LengthAwarePaginator
    {
        // Include products from the category itself and all its subcategories
        $childIds = Category::where('parent_id', $categoryId)->pluck('id');
        $categoryIds = $childIds->prepend($categoryId)->toArray();

        $query = Product::with(['translations', 'images', 'category.translations'])
            ->active()
            ->whereIn('category_id', $categoryIds);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function getRelated(Product $product, int $limit = 4): Collection
    {
        return Product::with(['translations', 'images'])
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit($limit)
            ->get();
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::with(['translations', 'images', 'variants', 'category', 'reviews.user'])
            ->where('slug', $slug)
            ->first();
    }

    public function updateStock(int $productId, int $quantity, bool $decrement = true): void
    {
        $product = Product::findOrFail($productId);

        if ($decrement) {
            $product->decrement('stock_quantity', $quantity);
        } else {
            $product->increment('stock_quantity', $quantity);
        }
    }

    public function getLowStock(): Collection
    {
        return Product::with(['translations'])
            ->lowStock()
            ->get();
    }

    private function applyFilters($query, array $filters): void
    {
        if (! empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (! empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        if (! empty($filters['plant_type'])) {
            $query->where('plant_type', $filters['plant_type']);
        }
        if (! empty($filters['sunlight'])) {
            $query->where('sunlight', $filters['sunlight']);
        }
        if (! empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }
        if (! empty($filters['sort'])) {
            match ($filters['sort']) {
                'price_asc' => $query->orderBy('price'),
                'price_desc' => $query->orderByDesc('price'),
                'newest' => $query->latest(),
                'oldest' => $query->oldest(),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }
    }
}
