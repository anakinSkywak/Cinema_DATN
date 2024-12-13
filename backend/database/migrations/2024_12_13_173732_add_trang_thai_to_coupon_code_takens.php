<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('coupon_code_takens', function (Blueprint $table) {
            $table->tinyInteger('trang_thai')->default(0)->comment('0: chưa sử dụng, 1: đã dùng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_code_takens', function (Blueprint $table) {
            //
        });
    }
};
