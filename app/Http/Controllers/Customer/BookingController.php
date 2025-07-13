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
use App\Models\RiwayatBooking;

class BookingController extends Controller
{
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

    public function confirmBooking($id, Request $request)
    {
        $user = $request->auth_user;
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Hanya admin yang diizinkan.',
            ], 403);
        }
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json([
                'status' => false,
                'message' => 'Booking tidak ditemukan.',
            ], 404);
        }
        try {
            DB::beginTransaction();
            // Simpan ke tabel riwayat
            RiwayatBooking::create([
                'id_user' => $booking->id_user,
                'id_kamar' => $booking->id_kamar,
                'nama_kamar' => $booking->nama_kamar,
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $booking->check_out_date,
                'harga' => $booking->harga,
                'status' => 'selesai',
            ]);
            // Hapus dari bookings
            $booking->delete();
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Booking berhasil dikonfirmasi dan dipindah ke riwayat.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memproses booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRiwayatByUser(Request $request)
    {
        $user = $request->auth_user;
        $riwayat = DB::table('riwayat_bookings as rb')
            ->join('kamar_hotels as k', 'rb.id_kamar', '=', 'k.id')
            ->join('hotels as h', 'k.id_hotel', '=', 'h.id')
            ->where('rb.id_user', $user->id)
            ->orderBy('rb.check_in_date', 'desc')
            ->select(
                'rb.id',
                'rb.nama_kamar',
                'rb.check_in_date',
                'rb.check_out_date',
                'rb.harga',
                'rb.status',
                'h.nama_hotel'
            )
            ->get();
        return response()->json([
            'status' => true,
            'message' => 'Data riwayat berhasil diambil.',
            'data' => $riwayat
        ]);
    }
}
