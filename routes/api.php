<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\KamarHotelController;
use App\Http\Controllers\Customer\BookingController;

// Auth routes (tanpa token)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public (tanpa login)
Route::get('/hotels', [HotelController::class, 'getAll']);
Route::get('/hotels/{id}/kamars', [KamarHotelController::class, 'getByHotel']);

// Private (perlu token manual)
Route::middleware('auth.check')->group(function () {
    // Customer
    Route::post('/customer/booking', [BookingController::class, 'store']);

    // Admin - Add - Hotel
    Route::post('/admin/hotel', [HotelController::class, 'store']);

    // Admin - Get - Hotel
    Route::get('/admin/hotel', [HotelController::class, 'index']);

    // Cek Hotel admin
    Route::get('/admin/hotel/check', [HotelController::class, 'checkHotel']);

    // Admin - Add - Kamar Hotel
    Route::post('/admin/kamar-hotel', [KamarHotelController::class, 'store']);

    // Admin - Update - Kamar Hotel
    Route::put('/admin/kamar/{id}', [KamarHotelController::class, 'update']);

    // Admin - Delete - Kamar Hotel
    Route::delete('/admin/kamar/{id}', [KamarHotelController::class, 'destroy']);

    // get kamar hotel by admin
    Route::get('/admin/kamar-hotel', [KamarHotelController::class, 'index']);

    // get booking by hotel admin
    Route::get('/admin/booking-by-hotel', [BookingController::class, 'getByHotel']);

    // Get Profile
    Route::get('/profile', [AuthController::class, 'profile']);

    // Get Data Booking
    Route::get('/customer/booking', [BookingController::class, 'getByUser']);

    // SEARCH BY ALAMAT
    Route::get('/admin/hotel-search', [HotelController::class, 'searchByAlamat']);
});
