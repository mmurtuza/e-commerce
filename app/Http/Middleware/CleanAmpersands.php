<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CleanAmpersands
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $queryString = $request->server('QUERY_STRING');

        if ($queryString && str_contains($queryString, '&amp;')) {
            $cleanedQueryString = str_replace('&amp;', '&', $queryString);

            // Redirect to the URL with the corrected query string
            $newUrl = $request->url().'?'.$cleanedQueryString;

            return redirect()->to($newUrl);
        }

        return $next($request);
    }
}
