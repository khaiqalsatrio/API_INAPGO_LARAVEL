<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\KamarHotel;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreBookingRequest;

class BookingController extends Controller
{
    // public function store(Request $request)
    // {
    //     $user = $request->auth_user;
    //     $validator = Validator::make($request->all(), [
    //         'id_kamar' => 'required|exists:kamar_hotels,id',
    //         'check_in_date' => 'required|date|date_format:Y-m-d',
    //         'check_out_date' => 'required|date|after:check_in_date|date_format:Y-m-d',
    //     ], [
    //         'check_out_date.after' => 'Tanggal check-out harus setelah tanggal check-in.',
    //         'check_in_date.required' => 'Tanggal check-in wajib diisi.',
    //         'check_out_date.required' => 'Tanggal check-out wajib diisi.',
    //         'check_in_date.date' => 'Format tanggal check-in tidak valid.',
    //         'check_out_date.date' => 'Format tanggal check-out tidak valid.',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validasi gagal',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }
    //     $kamar = KamarHotel::find($request->id_kamar);
    //     if (!$kamar) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Kamar tidak ditemukan.'
    //         ], 404);
    //     }
    //     $booking = Booking::create([
    //         'id_user' => $user->id,
    //         'id_kamar' => $kamar->id,
    //         'nama_kamar' => $kamar->nama_kamar,
    //         'check_in_date' => $request->check_in_date,
    //         'check_out_date' => $request->check_out_date,
    //         'harga' => $kamar->harga,
    //         'status' => 'pending'
    //     ]);
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Booking berhasil dibuat.',
    //         'data' => $booking
    //     ]);
    // }

    public function store(StoreBookingRequest $request)
    {
        $user = $request->auth_user;
        $kamar = KamarHotel::find($request->id_kamar);
        if (!$kamar) {
            return response()->json([
                'status' => false,
                'message' => 'Kamar tidak ditemukan.'
            ], 404);
        }
        $booking = Booking::create([
            'id_user' => $user->id,
            'id_kamar' => $kamar->id,
            'nama_kamar' => $kamar->nama_kamar,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'harga' => $kamar->harga,
            'status' => 'pending'
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Booking berhasil dibuat.',
            'data' => $booking
        ]);
    }

    public function getByUser(Request $request)
    {
        $user = $request->auth_user;
        $bookings = Booking::with(['kamar.hotel'])
            ->where('id_user', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'id_user' => $booking->id_user,
                    'id_kamar' => $booking->id_kamar,
                    'nama_kamar' => $booking->nama_kamar,
                    'check_in_date' => $booking->check_in_date,
                    'check_out_date' => $booking->check_out_date,
                    'harga' => $booking->harga,
                    'status' => $booking->status,
                    'bukti_bayar' => $booking->bukti_bayar,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                    'nama_hotel' => $booking->kamar->hotel->nama_hotel ?? '-',
                ];
            });
        return response()->json([
            'status' => true,
            'message' => 'Data booking berhasil diambil.',
            'data' => $bookings
        ]);
    }

    public function getByHotel(Request $request)
    {
        $user = $request->auth_user;
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Hanya admin yang diizinkan.',
            ], 403);
        }
        $bookings = DB::table('bookings as b')
            ->join('kamar_hotels as k', 'b.id_kamar', '=', 'k.id')
            ->join('hotels as h', 'k.id_hotel', '=', 'h.id')
            ->join('users as u', 'b.id_user', '=', 'u.id')
            ->where('h.id_user', $user->id)
            ->orderBy('b.check_in_date', 'desc')
            ->select(
                'b.id',
                'b.check_in_date',
                'b.check_out_date',
                'b.status',
                'k.nama_kamar',
                'h.nama_hotel',
                'u.id as id_user',
                'u.nama as nama_user',
                'u.email as email_user'
            )
            ->get();
        return response()->json([
            'status' => true,
            'message' => 'Data booking berhasil diambil.',
            'data' => $bookings,
        ]);
    }
}
