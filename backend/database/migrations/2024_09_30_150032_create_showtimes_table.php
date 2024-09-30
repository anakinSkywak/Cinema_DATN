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
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->date('ngay_chieu');
            $table->integer('thoi_luong_chieu'); // Lưu thời lượng chiếu tính theo phút
            // khóa ngoại
            $table->unsignedBigInteger('phim_id');
            $table->foreign('phim_id')->references('id')->on('movies')->onDelete('cascade');

            $table->unsignedBigInteger('rapphim_id');
            $table->foreign('rapphim_id')->references('id')->on('theaters')->onDelete('cascade');

            $table->unsignedBigInteger('room_id');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
