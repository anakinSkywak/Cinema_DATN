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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('ten_phim', 255);
            $table->string('anh_phim', 255)->nullable();
            $table->string('dao_dien', 255)->nullable();
            $table->string('dien_vien', 255)->nullable();
            $table->string('noi_dung', 255);
            $table->string('trailer', 255);
            $table->decimal('gia_ve', 12 ,3);
            $table->decimal('danh_gia', 3 , 1);
            
            $table->unsignedBigInteger('loaiphim_id');
            $table->foreign('loaiphim_id')->references('id')->on('movie_genres')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
