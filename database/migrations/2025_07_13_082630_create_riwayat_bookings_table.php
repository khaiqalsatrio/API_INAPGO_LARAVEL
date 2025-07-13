<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_kamar')->nullable(); // ← penting!
            $table->string('nama_kamar');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('harga');
            $table->string('status')->default('selesai');
            $table->timestamps();

            $table->foreign('id_user')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('id_kamar')
                ->references('id')->on('kamar_hotels')
                ->onDelete('set null'); // sekarang valid karena nullable
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_bookings');
    }
};
