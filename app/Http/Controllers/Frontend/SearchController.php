<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController
{
    public function index(Request $request): View
    {
        $query = $request->get('q', '');
        $results = collect();

        if (strlen($query) >= 2) {
            $results = Product::with(['translations', 'images'])
                ->active()
                ->whereHas('translations', fn ($q) => $q->where('name', 'like', "%{$query}%"))
                ->paginate(16);
        }

        return view('search.index', compact('query', 'results'));
    }
}
