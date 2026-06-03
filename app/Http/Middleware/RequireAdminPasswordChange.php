<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Filament\Pages\SetPassword;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminPasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\Admin|null $admin */
        $admin = Filament::auth()->user();

        if ($admin && $admin->needsPasswordChange()) {
            $setPasswordUrl = SetPassword::getUrl(panel: 'admin');

            // Don't redirect if already on the set-password page (avoids loops)
            if ($request->url() !== $setPasswordUrl) {
                return redirect()->to($setPasswordUrl);
            }
        }

        return $next($request);
    }
}
