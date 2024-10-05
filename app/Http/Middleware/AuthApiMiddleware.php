<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class AuthApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        // Check for the Authorization header
        $authHeader = $request->header('Authorization');
        Log::info('Authorization Header: ' . $authHeader);

        if (!$authHeader) {
            return response()->json(['error' => 'No token provided'], Response::HTTP_UNAUTHORIZED);
        }

        // Ensure the token format is correct
        if (!preg_match('/^Bearer\s(\S+)$/', $authHeader, $matches)) {
            return response()->json(['error' => 'Invalid token format'], Response::HTTP_UNAUTHORIZED);
        }

        //$token = $matches[1];

        try {
            // Try to authenticate the user via JWT token
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
            }

            // Set the user in the request for later use
            $request->merge(['user' => $user]);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to authenticate token'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
