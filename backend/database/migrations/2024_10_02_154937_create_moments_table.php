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
        Schema::create('moments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('phim_id')->constrained('movies')->onDelete('cascade');
            $table->string('anh_khoang_khac', 255)->nullable(); // Ảnh có thể nullable
            $table->string('noi_dung', 255);
            $table->integer('like')->default(0); // Số like mặc định là 0
            $table->integer('dislike')->default(0); // Số dislike mặc định là 0
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moments');
    }
};
