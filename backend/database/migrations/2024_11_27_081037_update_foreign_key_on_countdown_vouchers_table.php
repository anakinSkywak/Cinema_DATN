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
        Schema::table('countdown_vouchers', function (Blueprint $table) {
            // Xóa khóa ngoại cũ
            // $table->dropForeign(['magiamgia_id']);

            // Thêm khóa ngoại mới
            $table->foreign('magiamgia_id')->references('id')->on('coupons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countdown_vouchers', function (Blueprint $table) {
            // Xóa khóa ngoại mới
            // $table->dropForeign(['magiamgia_id']);

            // Thêm lại khóa ngoại cũ
            $table->foreign('magiamgia_id')->references('id')->on('vouchers');
        });
    }
};
