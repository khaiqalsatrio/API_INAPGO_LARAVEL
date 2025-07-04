<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKamarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kamar' => 'sometimes|required|string',
            'harga' => 'sometimes|required|numeric',
            'stok_kamar' => 'sometimes|required|integer',
            'deskripsi_kamar' => 'sometimes|required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kamar.required' => 'Nama kamar tidak boleh kosong.',
            'harga.required' => 'Harga wajib diisi.',
            'stok_kamar.required' => 'Stok kamar wajib diisi.',
            'deskripsi_kamar.required' => 'Deskripsi kamar wajib diisi.',
        ];
    }
}
