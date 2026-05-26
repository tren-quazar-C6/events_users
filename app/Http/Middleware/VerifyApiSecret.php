<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.tickets_api_secret');

        if (!$secret || $request->bearerToken() !== $secret) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        return $next($request);
    }
}
