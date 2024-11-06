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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->references('id')->on('bookings')->onDelete('cascade');
            $table->decimal('tong_tien', 12, 3);
            $table->string('tien_te', 10)->default('VND');
            $table->enum('phuong_thuc_thanh_toan', ['vietqr','vnpay','viettel_money','payoo']); 
            $table->string('ma_thanh_toan',);
            $table->string('ma_tham_chieu')->nullable();
            $table->dateTime('ngay_thanh_toan');
            $table->enum('trang_thai', ['Đang chờ xử lý','Đã hoàn thành','Không thành công','Đã hoàn lại','Đã hủy'])->default('Đang chờ xử lý');
            $table->json('chi_tiet_giao_dich')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('registermember_id')->nullable()->references('id')->on('register_members')->onDelete('set null');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
