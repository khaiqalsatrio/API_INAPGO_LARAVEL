<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->binary('hotel_image_blob')->nullable()->after('kota');
            $table->string('hotel_image_mime', 255)->nullable()->after('hotel_image_blob');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('hotel_image_blob');
            $table->dropColumn('hotel_image_mime');
        });
    }
};
