<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AuthCheck
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak ditemukan atau format salah.',
            ], 401);
        }
        $token = substr($header, 7);
        $user = User::where('token', $token)
            ->where('token_expired_at', '>', now())
            ->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid atau sudah kadaluarsa.',
            ], 401);
        }
        $request->merge(['auth_user' => $user]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        return $next($request);
    }
}
