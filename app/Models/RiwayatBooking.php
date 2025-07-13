<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatBooking extends Model
{
    use HasFactory;

    protected $table = 'riwayat_bookings';

    protected $fillable = [
        'id_user',
        'id_kamar',
        'nama_kamar',
        'check_in_date',
        'check_out_date',
        'harga',
        'status',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi ke Kamar
    public function kamar()
    {
        return $this->belongsTo(KamarHotel::class, 'id_kamar');
    }
    
}
