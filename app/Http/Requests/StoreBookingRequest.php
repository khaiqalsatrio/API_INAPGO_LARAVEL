<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Pastikan true jika tidak ingin dibatasi
    }

    public function rules(): array
    {
        return [
            'id_kamar' => 'required|exists:kamar_hotels,id',
            'check_in_date' => 'required|date|date_format:Y-m-d',
            'check_out_date' => 'required|date|after:check_in_date|date_format:Y-m-d',
        ];
    }

    public function messages(): array
    {
        return [
            'check_out_date.after' => 'Tanggal check-out harus setelah tanggal check-in.',
            'check_in_date.required' => 'Tanggal check-in wajib diisi.',
            'check_out_date.required' => 'Tanggal check-out wajib diisi.',
            'check_in_date.date' => 'Format tanggal check-in tidak valid.',
            'check_out_date.date' => 'Format tanggal check-out tidak valid.',
        ];
    }
}
