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
        Schema::create('coupon_code_takens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('countdownvoucher_id')->nullable()->references('id')->on('countdown_vouchers')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->date('ngay_nhan')->nullable();
            $table->date('ngay_het_han')->nullable();
            $table->tinyInteger('trang_thai')->nullable()->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_code_takens');
    }
};
