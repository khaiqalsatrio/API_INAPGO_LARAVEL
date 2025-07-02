<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KamarHotel;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;

class KamarHotelController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->get('auth_user'); 
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Hanya admin yang bisa menambahkan kamar.'
            ], 403);
        }
        $request->validate([
            'nama_kamar' => 'required|string',
            'harga' => 'required|numeric',
            'stok_kamar' => 'required|integer',
            'deskripsi_kamar' => 'required|string',
        ]);
        // Cari hotel berdasarkan user admin
        $hotel = Hotel::where('id_user', $user->id)->first(); 
        if (!$hotel) {
            return response()->json([
                'status' => false,
                'message' => 'Hotel tidak ditemukan untuk admin ini.'
            ], 404);
        }
        $kamar = KamarHotel::create([
            'id_user' => $user->id,
            'id_hotel' => $hotel->id,
            'nama_kamar' => $request->nama_kamar,
            'harga' => $request->harga,
            'stok_kamar' => $request->stok_kamar,
            'deskripsi_kamar' => $request->deskripsi_kamar,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Kamar berhasil ditambahkan.',
            'data' => $kamar
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->get('auth_user'); 
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Hanya admin yang bisa melihat daftar kamar.'
            ], 403);
        }
        $hotel = Hotel::where('id_user', $user->id)->first();
        if (!$hotel) {
            return response()->json([
                'status' => false,
                'message' => 'Kamar tidak ditemukan untuk admin ini.'
            ], 404);
        }
        $kamars = KamarHotel::where('id_hotel', $hotel->id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Data kamar berhasil diambil.',
            'data' => $kamars
        ]);
    }

    public function getByHotel($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return response()->json([
                'status' => false,
                'message' => 'Hotel tidak ditemukan.'
            ], 404);
        }
        $kamars = KamarHotel::where('id_hotel', $id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Daftar kamar berhasil diambil.',
            'data' => $kamars
        ]);
    }

    // UPDATE DATA KAMAR
    public function update(Request $request, $id)
    {
        $user = $request->get('auth_user');
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Hanya admin yang bisa mengupdate kamar.'
            ], 403);
        }
        $kamar = KamarHotel::where('id', $id)
            ->where('id_user', $user->id)
            ->first();
        if (!$kamar) {
            return response()->json([
                'status' => false,
                'message' => 'Kamar tidak ditemukan atau bukan milik admin ini.'
            ], 404);
        }
        $request->validate([
            'nama_kamar' => 'sometimes|required|string',
            'harga' => 'sometimes|required|numeric',
            'stok_kamar' => 'sometimes|required|integer',
            'deskripsi_kamar' => 'sometimes|required|string',
        ]);
        $kamar->update($request->only([
            'nama_kamar',
            'harga',
            'stok_kamar',
            'deskripsi_kamar',
        ]));
        return response()->json([
            'status' => true,
            'message' => 'Kamar berhasil diperbarui.',
            'data' => $kamar
        ]);
    }
    
    // DELETE DATA KAMAR
    public function destroy(Request $request, $id)
    {
        $user = $request->get('auth_user');
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Hanya admin yang bisa menghapus kamar.'
            ], 403);
        }
        $kamar = KamarHotel::where('id', $id)
            ->where('id_user', $user->id)
            ->first();
        if (!$kamar) {
            return response()->json([
                'status' => false,
                'message' => 'Kamar tidak ditemukan atau bukan milik admin ini.'
            ], 404);
        }
        $kamar->delete();
        return response()->json([
            'status' => true,
            'message' => 'Kamar berhasil dihapus.'
        ]);
    }
}
