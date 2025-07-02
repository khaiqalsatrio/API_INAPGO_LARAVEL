<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $table = 'hotels';

    protected $fillable = [
        'id_user',
        'nama_hotel',
        'deskripsi_hotel',
        'latitude',
        'longitude',
        'alamat',
        'kota', 
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Optional: jika kamu mau akses kamar-kamarnya
    public function kamars()
    {
        return $this->hasMany(KamarHotel::class, 'id_hotel');
    }
}
