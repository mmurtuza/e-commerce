<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController
{
    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $exists = Review::where('user_id', auth()->id())
            ->where('product_id', $request->integer('product_id'))
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        Review::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'is_approved' => false,
        ]);

        return back()->with('success', 'Review submitted. It will appear after approval.');
    }
}
