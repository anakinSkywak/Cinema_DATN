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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            
            // Liên kết với bảng users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Liên kết với bảng movies
            $table->foreignId('movie_id')->constrained('movies')->onDelete('cascade');
            
            // Liên kết với bảng moments
            $table->foreignId('moment_id')->constrained('moments')->onDelete('cascade');
            
            $table->text('noi_dung'); // Nội dung bình luận
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
