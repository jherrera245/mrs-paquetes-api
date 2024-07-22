<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
            }

            if ($user->status != 1) {
                JWTAuth::invalidate(JWTAuth::getToken());
                return response()->json(['message' => 'Account is inactive'], Response::HTTP_FORBIDDEN);
            }

            if ($user->hasAccessChanged()) {
                JWTAuth::invalidate(JWTAuth::getToken());
                return response()->json(['message' => 'Access changed. Please log in again.'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Could not authenticate user', 'error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
