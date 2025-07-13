<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class HotelController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->get('auth_user');
        $id_user = $user->id;
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

    public function index(Request $request)
    {
        $user = $request->auth_user;
        $hotels = Hotel::where('id_user', $user->id)->get();
        $hotelsWithImageUrl = $hotels->map(function ($hotel) {
            return [
                'id' => $hotel->id,
                'nama_hotel' => $hotel->nama_hotel,
                'deskripsi_hotel' => $hotel->deskripsi_hotel,
                'alamat' => $hotel->alamat,
                'kota' => $hotel->kota,
                'latitude' => $hotel->latitude,
                'longitude' => $hotel->longitude,
                'image_url' => $hotel->hotel_image_blob
                    ? url('api/hotel/image/' . $hotel->id)
                    : null,
            ];
        });
        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengambil data hotel.',
            'data' => $hotelsWithImageUrl,
        ]);
    }

    public function getAll()
    {
        $hotels = Hotel::select(
            'id',
            'nama_hotel',
            'deskripsi_hotel',
            'alamat',
            'kota',
            'latitude',
            'longitude',
            'id_user',
            'hotel_image_blob'
        )->get();
        $data = $hotels->map(function ($hotel) {
            return [
                'id' => $hotel->id,
                'nama_hotel' => $hotel->nama_hotel,
                'deskripsi_hotel' => $hotel->deskripsi_hotel,
                'alamat' => $hotel->alamat,
                'kota' => $hotel->kota,
                'latitude' => $hotel->latitude,
                'longitude' => $hotel->longitude,
                'id_user' => $hotel->id_user,
                'image_url' => $hotel->hotel_image_blob
                    ? url('api/hotel/image/' . $hotel->id)
                    : null,
            ];
        });
        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengambil semua data hotel.',
            'data' => $data
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

    public function searchByAlamat(Request $request)
    {
        $keyword = strtolower(trim(preg_replace('/\s+/', ' ', $request->query('keyword'))));
        if (!$keyword) {
            return response()->json([
                'status' => false,
                'message' => 'Keyword pencarian tidak boleh kosong.',
            ], 400);
        }
        $hotels = Hotel::where(function ($query) use ($keyword) {
            $query->whereRaw('LOWER(kota) LIKE ?', ["%{$keyword}%"])
                ->orWhereRaw('LOWER(nama_hotel) LIKE ?', ["%{$keyword}%"])
                ->orWhereRaw('LOWER(alamat) LIKE ?', ["%{$keyword}%"]);
        })->get();
        $hotelsWithImageUrl = $hotels->map(function ($hotel) {
            return [
                'id' => $hotel->id,
                'nama_hotel' => $hotel->nama_hotel,
                'deskripsi_hotel' => $hotel->deskripsi_hotel,
                'alamat' => $hotel->alamat,
                'kota' => $hotel->kota,
                'latitude' => $hotel->latitude,
                'longitude' => $hotel->longitude,
                'id_user' => $hotel->id_user,
                'image_url' => $hotel->hotel_image_blob
                    ? url('api/hotel/image/' . $hotel->id)
                    : null,
            ];
        });
        return response()->json([
            'status' => true,
            'message' => 'Hasil pencarian hotel.',
            'data' => $hotelsWithImageUrl,
        ]);
    }

    public function uploadHotelImage(Request $request)
    {
        $user = $request->get('auth_user');
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif,webp,bmp,tiff,heif,heic|max:5120',
        ]);
        try {
            $file = $request->file('image');
            $content = file_get_contents($file->getRealPath());
            // Ambil hotel milik user
            $hotel = Hotel::where('id_user', $user->id)->first();
            if (!$hotel) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hotel belum terdaftar untuk user ini.',
                ], 404);
            }
            $isUpdate = !is_null($hotel->hotel_image_blob);
            $hotel->hotel_image_blob = $content;
            $hotel->hotel_image_mime = $file->getMimeType();
            $hotel->save();
            return response()->json([
                'status' => true,
                'message' => $isUpdate
                    ? 'Foto hotel berhasil diperbarui.'
                    : 'Foto hotel berhasil diunggah.',
                'image_url' => $hotel->hotel_image_blob
                    ? url('api/hotel/image/' . $hotel->id)
                    : null,
                'file_size' => strlen($content),
                'file_hash' => md5($content),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan gambar hotel: ' . $e->getMessage(), [
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

    public function showImageByToken(Request $request)
    {
        $user = $request->get('auth_user');
        $hotel = Hotel::where('id_user', $user->id)->first();
        if (!$hotel || !$hotel->hotel_image_blob) {
            return response()->json([
                'status' => false,
                'message' => 'Gambar hotel tidak ditemukan.',
            ], 404);
        }
        return response($hotel->hotel_image_blob, 200)
            ->header('Content-Type', $hotel->hotel_image_mime);
    }

    public function showImageById($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel || !$hotel->hotel_image_blob) {
            return response()->json([
                'status' => false,
                'message' => 'Gambar tidak ditemukan.',
            ], 404);
        }
        return response($hotel->hotel_image_blob, 200)
            ->header('Content-Type', $hotel->hotel_image_mime);
    }
}
