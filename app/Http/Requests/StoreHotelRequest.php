<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // izinkan semua request (bisa dikontrol pakai middleware auth)
    }

    public function rules(): array
    {
        return [
            'nama_hotel' => 'required|string',
            'deskripsi_hotel' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'alamat' => 'required|string',
            'kota' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_hotel.required' => 'Nama hotel wajib diisi.',
            'deskripsi_hotel.required' => 'Deskripsi wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'kota.required' => 'Kota wajib diisi.',
        ];
    }
}
