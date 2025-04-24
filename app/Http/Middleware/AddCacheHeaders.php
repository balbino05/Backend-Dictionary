<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->isMethod('GET')) {
            $response->header('X-Response-Time', round((microtime(true) - LARAVEL_START) * 1000).'ms');

            if (Cache::has($request->url())) {
                $response->header('X-Cache', 'HIT');
            } else {
                $response->header('X-Cache', 'MISS');
            }
        }

        return $response;
    }
}
