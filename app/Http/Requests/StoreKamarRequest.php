<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKamarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kamar' => 'required|string',
            'harga' => 'required|numeric',
            'stok_kamar' => 'required|integer',
            'deskripsi_kamar' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kamar.required' => 'Nama kamar wajib diisi.',
            'harga.required' => 'Harga wajib diisi.',
            'stok_kamar.required' => 'Stok kamar wajib diisi.',
            'deskripsi_kamar.required' => 'Deskripsi kamar wajib diisi.',
        ];
    }
}
