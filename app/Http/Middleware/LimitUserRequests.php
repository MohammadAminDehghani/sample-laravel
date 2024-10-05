<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis; // Or use a database

class LimitUserRequests
{
    public function handle($request, Closure $next){
        $ip = $request->ip(); // Track by IP for unauthenticated users
        $userId = \auth()->check() ? \auth()->id() : $ip; // Use ID for authenticated users, IP otherwise

        //return response()->json(['id' => $userId], 403);
        $requestCount = Redis::get("request_count_{$userId}") ?? 0;

        Log::info('Request count for user ' . $userId . ': ' . $requestCount);


        // If unauthenticated and over 5 requests, redirect to login
        if (!Auth::check() && $requestCount >= 5) {
        return response()->json(['error' => 'Please login to continue'], 403);
    }

    // If over 15 requests, prompt to pay for packages
    if ($requestCount >= 15) {
        return response()->json(['error' => 'Request limit exceeded, please purchase a package'], 403);
    }

    // Increment request count
    Redis::incr("request_count_{$userId}");

    // Optionally, you can set an expiration time for the request count
    Redis::expire("request_count_{$userId}", 3600); // 1 hour limit

    return $next($request);
    }
}
