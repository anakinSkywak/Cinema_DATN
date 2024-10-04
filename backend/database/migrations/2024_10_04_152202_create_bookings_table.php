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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('thongtinchieu_id')->references('id')->on('showtimes');
            $table->integer('so_luong');
            $table->string('ghi_chu', 255);
            $table->string('ma_giam_gia', 255);
            $table->foreignId('doan_id')->references('id')->on('foods');
            $table->decimal('tong_tien',12,3);
            $table->decimal('tong_tien_thanh_toan' , 12,3);
            $table->date('ngay_mua');
            $table->tinyInteger('trang_thai')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
