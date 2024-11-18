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
            // Đảm bảo trường so_luong là số nguyên dương (unsigned)
            $table->unsignedInteger('so_luong')->change(); // Đảm bảo so_luong là unsigned integer
    
            // Thêm trường so_luong_con_lai là số nguyên dương
            $table->unsignedInteger('so_luong_con_lai')->default(0)->after('so_luong');

    
            // Cập nhật trang_thai, mặc định 0 là chưa sử dụng, 1 là đã sử dụng, có thể null
            $table->tinyInteger('trang_thai')->default(0)->nullable()->change();
        });
    }
    
    public function down(): void
    {
        Schema::table('countdown_vouchers', function (Blueprint $table) {
            // Rollback lại kiểu trường so_luong về integer thông thường (không unsigned)
            $table->integer('so_luong')->change();
    
            // Xóa trường so_luong_con_lai
            $table->dropColumn('so_luong_con_lai');
    
            // Khôi phục trang_thai thành không nullable và giá trị mặc định là 0
            $table->tinyInteger('trang_thai')->default(0)->nullable(false)->change();
        });
    }
    
};
