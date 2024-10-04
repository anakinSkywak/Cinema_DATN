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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('ma_giam_gia', 255);
            $table->decimal('muc_giam_gia', 12,3);
            $table->string('mota',255);
            $table->date('ngay_het_han');
            $table->integer('so_luong');
            $table->integer('so_luong_da_su_dung');
            $table->tinyInteger('trang_thai')->default(0)->nullable(); // 0 la dc su dung 1 la khoa voucher
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
