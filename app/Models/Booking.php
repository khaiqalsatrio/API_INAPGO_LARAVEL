<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'id_user',
        'id_kamar',
        'nama_kamar',
        'check_in_date',
        'check_out_date',
        'harga',
        'status'
    ];

    // Jika nama tabel tidak sesuai konvensi, bisa ditambahkan ini:
    // protected $table = 'bookings';

    /**
     * Relasi ke user (customer yang booking)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relasi ke kamar hotel
     */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(KamarHotel::class, 'id_kamar');
    }
}
