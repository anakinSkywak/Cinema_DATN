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
            // Sửa trường so_luong thành số nguyên dương
            $table->unsignedInteger('so_luong')->change();

            // Thêm trường so_luong_con_lai, cũng là số nguyên dương
            $table->unsignedInteger('so_luong_con_lai')->nullable()->after('so_luong');

            // Cập nhật trường trang_thai, mặc định 0 là chưa sử dụng, 1 là đã sử dụng
            $table->tinyInteger('trang_thai')->default(0)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('countdown_vouchers', function (Blueprint $table) {
            // Rollback các thay đổi
            $table->integer('so_luong')->change(); // Quay lại kiểu integer
            $table->dropColumn('so_luong_con_lai');
            $table->tinyInteger('trang_thai')->default(0)->nullable(false)->change();
        });
    }
};
