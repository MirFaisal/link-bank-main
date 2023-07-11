<?php

namespace App\Http\Middleware;

use App\Models\AuthToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $header = 'bal';
        $headerAuthToken = $request->header('x-y-z-meow-auth-token');
        $headerUserId = $request->header('x-y-z-meow-id');
        $authToken = AuthToken::where('id', $headerUserId)->where('token', $headerAuthToken)->where('killed_at', null)->first();
        if (!$authToken) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'msg' => 'Chutmaranir Pola'
                ]
            ]);
        }

        return $next($request);

    }
}