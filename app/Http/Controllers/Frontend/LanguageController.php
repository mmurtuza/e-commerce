<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $supported = ['bn', 'en'];

        if (! in_array($locale, $supported)) {
            abort(400);
        }

        Session::put('locale', $locale);

        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        return back()->withHeaders(['Vary' => 'Accept-Language']);
    }
}
