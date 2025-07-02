<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KamarHotel extends Model
{
    use HasFactory;

    protected $table = 'kamar_hotels';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_hotel',
        'id_user',
        'nama_kamar',
        'harga',
        'stok_kamar',
        'deskripsi_kamar'
    ];

    // ✅ Tambahkan ini untuk relasi ke Hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }
}
