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
        Schema::table('payments', function (Blueprint $table) {
            //'Đang chờ xử lý','Đã hoàn thành','Không thành công','Đã hoàn lại','Đã hủy'

            $table->tinyInteger('trang_thai')->default(0)->change(); // 0 Đang chờ xử lý , 1 Đã hoàn thành  2 Không thành công  , 3 Đã hủy, 4 Đã hoàn lại
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
