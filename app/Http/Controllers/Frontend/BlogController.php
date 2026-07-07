<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController
{
    public function index(Request $request): View
    {
        $query = Blog::with(['translations', 'author', 'category'])->published()->latest();

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->get('category')));
        }

        return view('blog.index', [
            'blogs' => $query->paginate(12),
            'categories' => BlogCategory::with('translations')->withCount('blogs')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $blog = Blog::with(['translations', 'author', 'category.translations'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('blog.show', compact('blog'));
    }
}
