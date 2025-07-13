<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Pastikan UserFactory Anda juga menggunakan 'nama'
        User::factory()->create([
            'nama' => 'Test User', // <-- UBAH DARI 'name' MENJADI 'nama'
            'email' => 'test@example.com',
            // Tambahkan juga 'role' jika Anda memiliki kolom 'role' di tabel users
            // dan ingin mengatur role default untuk user yang dibuat seeder.
            'role' => 'customer', // Contoh: tambahkan ini jika ada kolom 'role'
        ]);

        // Jika Anda juga mengaktifkan User::factory(10)->create();
        // Pastikan UserFactory Anda di database/factories/UserFactory.php
        // juga sudah diperbaiki untuk menggunakan 'nama'
    }
}