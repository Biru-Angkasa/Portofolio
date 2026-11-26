<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicCacheHeaders
{
    /**
     * Add edge-cacheable headers to public GET/HEAD portfolio responses.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethodCacheable() && $response->getStatusCode() === 200) {
            $response->headers->set(
                'Cache-Control',
                'public, max-age=300, s-maxage=3600, stale-while-revalidate=86400, stale-if-error=604800'
            );
        }

        return $response;
    }
}
