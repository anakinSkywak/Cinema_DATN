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
        Schema::create('countdown_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magiamgia_id')->references('id')->on('vouchers');
            $table->date('ngay');
            $table->time('thoi_gian_bat_dau');
            $table->time('thoi_gian_ket_thuc');
            $table->integer('so_luong');
            $table->tinyInteger('trang_thai')->default(0)->nullable(); // con the san 1 la da het ma
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countdown_vouchers');
    }
};
