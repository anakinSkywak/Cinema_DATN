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
        Schema::create('seat_showtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ghengoi_id')->references('id')->on('seats')->onDelete('cascade');
            $table->foreignId('thongtinchieu_id')->references('id')->on('showtimes')->onDelete('cascade');
            $table->tinyInteger('trang_thai')->default(0)->nullable(); // 0 la co the dat 1 da bi dat 2 la bao tri ghe
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_showtimes');
    }
};
