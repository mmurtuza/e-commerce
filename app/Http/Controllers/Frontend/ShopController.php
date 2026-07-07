<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['min_price', 'max_price', 'plant_type', 'sunlight', 'difficulty', 'sort']);
        $categorySlug = $request->get('category');

        $category = $categorySlug ? $this->categoryRepository->findBySlug($categorySlug) : null;

        $products = $category
            ? $this->productRepository->getByCategory($category->id, $filters)
            : $this->productRepository->getActive($filters);

        // Determine the parent category for breadcrumb and subcategory display
        $parentCategory = $category?->parent_id
            ? $this->categoryRepository->findById($category->parent_id)
            : null;

        // If browsing a parent, its children are the subcategories to show as tabs
        $subcategories = ($category && ! $category->parent_id)
            ? $category->children()->with('translations')->active()->orderBy('sort_order')->get()
            : collect();

        return view('shop.index', [
            'products' => $products,
            'categories' => $this->categoryRepository->getTree(),
            'currentCategory' => $category,
            'parentCategory' => $parentCategory,
            'subcategories' => $subcategories,
            'filters' => $filters,
        ]);
    }

    public function show(string $slug): View
    {
        $product = $this->productRepository->findBySlug($slug);

        abort_if(! $product || ! $product->is_active, 404);

        return view('shop.show', [
            'product' => $product,
            'related' => $this->productRepository->getRelated($product),
        ]);
    }
}
