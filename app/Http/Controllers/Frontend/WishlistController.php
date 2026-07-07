<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController
{
    public function index(): View
    {
        $wishlists = auth()->user()->wishlists()->with('product.translations', 'product.images')->get();

        return view('customer.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate(['product_id' => ['required', 'exists:products,id']]);

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->integer('product_id'))
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['added' => false, 'message' => 'Removed from wishlist']);
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $request->integer('product_id'),
        ]);

        return response()->json(['added' => true, 'message' => 'Added to wishlist']);
    }
}
