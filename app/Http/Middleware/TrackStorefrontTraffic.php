<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\StorefrontTrafficService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackStorefrontTraffic
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && ! $request->is('admin*') && ! $request->is('api*') && ! $request->ajax()) {
            StorefrontTrafficService::record($request);
        }

        return $response;
    }
}
