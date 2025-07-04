<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }
        $token = Str::random(64);
        $expiredAt = Carbon::now()->addHour();
        $user->update([
            'token' => $token,
            'token_expired_at' => $expiredAt
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'expired_at' => $expiredAt->toDateTimeString(),
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $role = $request->input('role', 'customer');
        if ($role === 'admin') {
            $adminSecret = $request->input('admin_secret');
            if ($adminSecret !== 'super123') {
                return response()->json([
                    'status' => false,
                    'message' => 'Secret key admin salah atau tidak disertakan.'
                ], 403);
            }
        } else {
            $role = 'customer';
        }
        try {
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role,
                'token' => null,
                'token_expired_at' => null
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Registrasi berhasil.',
                'user_id' => $user->id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        $user = $request->get('auth_user');
        return response()->json([
            'status' => true,
            'message' => 'Data user berhasil diambil.',
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
                'token_expired_at' => $user->token_expired_at,
            ]
        ]);
    }
}
