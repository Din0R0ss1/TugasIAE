<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan',
                ], 401);
            }

        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token sudah expired, silakan login ulang',
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Token tidak valid',
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token tidak ditemukan',
            ], 401);
        }

        return $next($request);
    }
}