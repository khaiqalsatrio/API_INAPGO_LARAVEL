<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_hotel' => 'required|string',
            'deskripsi_hotel' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'alamat' => 'required|string',
            'kota' => 'required|string',
        ]);
        $user = $request->get('auth_user');
        $id_user = $user->id;
        // Cek duplikasi
        $exists = Hotel::where('id_user', $id_user)
            ->where('alamat', $request->alamat)
            ->exists();
        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Hotel dengan alamat ini sudah terdaftar.'
            ], 400);
        }
        $hotel = Hotel::create([
            'id_user' => $id_user,
            'nama_hotel' => $request->nama_hotel,
            'deskripsi_hotel' => $request->deskripsi_hotel,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'alamat' => $request->alamat,
            'kota' => $request->kota,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Hotel berhasil ditambahkan.',
            'data' => $hotel
        ]);
    }

    // GET All HOTEL
    public function index(Request $request)
    {
        $user = $request->auth_user;
        $hotels = Hotel::where('id_user', $user->id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengambil data hotel.',
            'data' => $hotels
        ]);
    }


    public function getAll()
    {
        $hotels = Hotel::all();
        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengambil semua data hotel.',
            'data' => $hotels
        ]);
    }

    public function checkHotel(Request $request)
    {
        $user = $request->auth_user;
        $hasHotel = Hotel::where('id_user', $user->id)->exists();
        return response()->json([
            'status' => $hasHotel,
            'message' => $hasHotel
                ? 'Admin sudah mendaftarkan hotel.'
                : 'Admin belum mendaftarkan hotel.',
        ]);
    }


    // SEARCH BY ALAMAT
    public function searchByAlamat(Request $request)
    {
        // Ambil dan normalisasi keyword
        $keyword = strtolower(trim(preg_replace('/\s+/', ' ', $request->query('keyword'))));
        // Validasi keyword tidak boleh kosong
        if (!$keyword) {
            return response()->json([
                'status' => false,
                'message' => 'Keyword pencarian tidak boleh kosong.',
            ], 400);
        }
        // Pencarian tidak case-sensitive di beberapa kolom
        $hotels = Hotel::where(function ($query) use ($keyword) {
            $query->whereRaw('LOWER(kota) LIKE ?', ["%{$keyword}%"])
                ->orWhereRaw('LOWER(nama_hotel) LIKE ?', ["%{$keyword}%"])
                ->orWhereRaw('LOWER(alamat) LIKE ?', ["%{$keyword}%"]);
        })->get();
        return response()->json([
            'status' => true,
            'message' => 'Hasil pencarian hotel berdasarkan kota, nama hotel, atau alamat.',
            'data' => $hotels
        ]);
    }
}
