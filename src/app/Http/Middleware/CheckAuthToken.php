<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CheckAuthToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token missing'], 401);
        }
        $url =config('apiConfig.auth_service.base_url').'user';
        $response = Http::withToken($token)
            ->get($url); // Replace with actual URL

        if ($response->status() !== 200) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Optionally store user info
        $request->merge(['auth_user' => $response->json()]);

        return $next($request);
    }
}

