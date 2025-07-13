<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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
                'image_url' => $user->profile_image_blob
                    ? url('api/profile/image/' . $user->id)
                    : null,
                'token_expired_at' => $user->token_expired_at,
            ]
        ]);
    }

    public function uploadProfileImage(Request $request)
    {
        $user = $request->get('auth_user');
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif,webp,bmp,tiff,heif,heic|max:5120',
        ]);
        try {
            $file = $request->file('image');
            $content = file_get_contents($file->getRealPath());
            // Cek apakah user sudah memiliki foto profil sebelumnya
            $isUpdate = !is_null($user->profile_image_blob);
            // Simpan/update gambar profil
            $user->profile_image_blob = $content;
            $user->profile_image_mime = $file->getMimeType();
            $user->save();
            return response()->json([
                'status' => true,
                'message' => $isUpdate
                    ? 'Foto profil berhasil diperbarui.'
                    : 'Foto profil berhasil diunggah.',
                'image_url' => url('api/profile/image/' . $user->id),
                'file_size' => strlen($content),
                'file_hash' => md5($content),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan gambar profil: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id ?? 'unknown',
                'file_name' => $file->getClientOriginalName() ?? 'unknown',
                'file_mime' => $file->getMimeType() ?? 'unknown',
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan gambar. Mohon coba lagi.',
                'error' => 'Terjadi kesalahan internal saat mengunggah gambar.',
            ], 500);
        }
    }

    public function showProfileImage($id)
    {
        $user = User::find($id);
        if (!$user || !$user->profile_image_blob || !$user->profile_image_mime) {
            return response()->json([
                'status' => false,
                'message' => 'Gambar tidak ditemukan.',
            ], 404);
        }
        return response($user->profile_image_blob, 200)
            ->header('Content-Type', $user->profile_image_mime);
    }
}
