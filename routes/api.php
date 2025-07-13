<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\KamarHotelController;
use App\Http\Controllers\Customer\BookingController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// 🟢 Endpoint customer / publik
Route::get('/hotels', [HotelController::class, 'getAll']);
Route::get('/hotels/{id}/kamars', [KamarHotelController::class, 'getByHotel']);
Route::get('/hotel/image/{id}', [HotelController::class, 'showImageById']); 
Route::get('/profile/image/{id}', [AuthController::class, 'showProfileImage']);

Route::middleware('auth.check')->group(function () {

    // CUSTOMER
    Route::post('/customer/booking', [BookingController::class, 'store']);
    Route::get('/customer/booking', [BookingController::class, 'getByUser']);

    // ADMIN - HOTEL
    Route::post('/admin/hotel', [HotelController::class, 'store']);
    Route::get('/admin/hotel', [HotelController::class, 'index']);
    Route::get('/admin/hotel/check', [HotelController::class, 'checkHotel']);
    Route::get('/admin/hotel-search', [HotelController::class, 'searchByAlamat']);

    // ADMIN - KAMAR
    Route::post('/admin/kamar-hotel', [KamarHotelController::class, 'store']);
    Route::put('/admin/kamar/{id}', [KamarHotelController::class, 'update']);
    Route::delete('/admin/kamar/{id}', [KamarHotelController::class, 'destroy']);
    Route::get('/admin/kamar-hotel', [KamarHotelController::class, 'index']);

    // ADMIN - Booking hotel milik admin
    Route::get('/admin/booking-by-hotel', [BookingController::class, 'getByHotel']);

    // PROFILE
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/uploadProfileImage', [AuthController::class, 'uploadProfileImage']);

    // UPLOAD & LIHAT GAMBAR HOTEL milik sendiri (ADMIN)
    Route::post('/uploadHotelImage', [HotelController::class, 'uploadHotelImage']);
    Route::get('/hotel/image', [HotelController::class, 'showImageByToken']); // hanya admin login

    Route::post('/booking/confirm/{id}', [BookingController::class, 'confirmBooking']);

    Route::get('/customer/riwayat-booking', [BookingController::class, 'getRiwayatByUser']);
});

