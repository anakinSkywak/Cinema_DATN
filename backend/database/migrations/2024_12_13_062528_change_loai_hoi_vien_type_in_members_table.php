<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLoaiHoiVienTypeInMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Thay đổi kiểu dữ liệu cột loai_hoi_vien từ ENUM thành VARCHAR(255)
        Schema::table('members', function (Blueprint $table) {
            $table->string('loai_hoi_vien', 255)->change();  // Chuyển thành VARCHAR(255)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Quay lại kiểu dữ liệu ENUM nếu cần
        Schema::table('members', function (Blueprint $table) {
            $table->enum('loai_hoi_vien', ['Thường', 'VIP'])->change();
        });
    }
}
