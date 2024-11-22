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
        Schema::table('vouchers', function (Blueprint $table) {
            // Thêm trường "gia_don_toi_thieu"
            $table->decimal('gia_don_toi_thieu', 15, 0)->nullable()->after('muc_giam_gia')->comment('Giá đơn tối thiểu để áp dụng mã giảm giá');

            // Xóa trường "ngay_het_han"
            $table->dropColumn('ngay_het_han');
        });
    }

    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // Khôi phục trường "ngay_het_han"
            $table->date('ngay_het_han')->after('mota');

            // Xóa trường "gia_don_toi_thieu"
            $table->dropColumn('gia_don_toi_thieu');
        });
    }
};
