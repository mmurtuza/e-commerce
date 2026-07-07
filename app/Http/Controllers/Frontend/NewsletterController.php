<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\SubscribeNewsletterRequest;
use App\Models\Newsletter;
use Illuminate\Http\JsonResponse;

class NewsletterController
{
    public function subscribe(SubscribeNewsletterRequest $request): JsonResponse
    {
        Newsletter::updateOrCreate(
            ['email' => $request->validated('email')],
            ['is_subscribed' => true, 'subscribed_at' => now(), 'unsubscribed_at' => null],
        );

        return response()->json(['success' => true, 'message' => 'Subscribed successfully!']);
    }
}
