<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Banner;
use App\Models\Blog;
use App\Models\Review;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\View\View;

class HomeController
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function index(): View
    {
        return view('home', [
            'banners' => Banner::with('translations')->active()->where('type', 'hero_slider')->orderBy('sort_order')->get(),
            'categories' => $this->categoryRepository->getWithProductCount(),
            'featured' => $this->productRepository->getFeatured(8),
            'newArrivals' => $this->productRepository->getNewArrivals(8),
            'reviews' => Review::with(['user', 'product.translations', 'product.images'])->approved()->latest()->limit(6)->get(),
            'blogs' => Blog::with(['translations', 'category.translations'])->published()->latest()->limit(3)->get(),
        ]);
    }
}
