<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- PENTING: Pastikan baris ini ada!

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menggunakan DB::statement untuk menambahkan kolom LONGBLOB secara langsung
            // Ini adalah cara yang universal dan tidak bergantung pada helper Laravel spesifik versi
            // Kolom akan ditambahkan setelah 'token_expired_at'
            DB::statement('ALTER TABLE users ADD COLUMN profile_image_blob LONGBLOB NULL AFTER token_expired_at');
            
            // Menambahkan kolom profile_image_mime
            $table->string('profile_image_mime')->nullable()->after('profile_image_blob');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_image_blob', 'profile_image_mime']);
        });
    }
};