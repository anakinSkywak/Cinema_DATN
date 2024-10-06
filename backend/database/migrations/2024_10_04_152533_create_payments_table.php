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
            $table->foreignId('booking_id')->references('id')->on('bookings');
            $table->decimal('tong_tien', 12,3);
            $table->enum('phuong_thuc_thanh_toan', ['credit_card', 'paypal', 'cash', 'bank_transfer']); // Phương thức thanh toán
            $table->string('ma_thanh_toan', 255)->unique();
            $table->dateTime('ngay_thanh_toan');
            $table->tinyInteger('trang_thai')->default(0)->nullable();
            $table->timestamps();
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
