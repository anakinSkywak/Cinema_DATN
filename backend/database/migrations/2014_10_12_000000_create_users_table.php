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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten', 255); // Giữ nguyên tên, bỏ unique để tránh giới hạn chỉ mục
            $table->string('anh', 255)->nullable(); // Nếu ảnh không bắt buộc
            $table->enum('gioi_tinh', ['nam', 'nu', 'khac']);
            $table->string('email')->unique();
            $table->string('so_dien_thoai', 10)->unique(); // Giới hạn số điện thoại
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['user', 'admin', 'nhan_vien'])->default('user'); // Sửa lỗi duplicate
            $table->unsignedInteger('diem_thuong')->default(0);
            $table->string('ma_giam_gia', 255)->nullable(); // Cho phép nullable nếu không bắt buộc
            
            // // Sửa lại phần khóa ngoại
            // $table->unsignedBigInteger('lienhe_id');
            // $table->foreign('lienhe_id')->references('id')->on('contacts')->onDelete('cascade');


            // // $table->foreignId('baiviet_id')->constrained('blogs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
