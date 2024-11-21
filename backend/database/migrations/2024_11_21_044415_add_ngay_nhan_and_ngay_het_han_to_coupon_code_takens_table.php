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
        Schema::table('coupon_code_takens', function (Blueprint $table) {
            $table->date('ngay_nhan')->nullable()->after('user_id'); // Thêm cột ngay_nhan
            $table->date('ngay_het_han')->nullable()->after('ngay_nhan'); // Thêm cột ngay_het_han
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_code_takens', function (Blueprint $table) {
            $table->dropColumn(['ngay_nhan', 'ngay_het_han']); // Xóa các cột khi rollback
        });
    }
};
