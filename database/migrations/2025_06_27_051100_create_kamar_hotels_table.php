<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar_hotels', function (Blueprint $table) {
            $table->id();

            // Foreign key user (admin pemilik kamar)
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');

            // Foreign key hotel
            $table->unsignedBigInteger('id_hotel');
            $table->foreign('id_hotel')->references('id')->on('hotels')->onDelete('cascade');

            $table->string('nama_kamar');
            $table->integer('harga');
            $table->integer('stok_kamar');
            $table->text('deskripsi_kamar');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar_hotels');
    }
};
