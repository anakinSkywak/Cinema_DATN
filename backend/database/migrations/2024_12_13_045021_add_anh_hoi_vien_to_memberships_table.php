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
        Schema::table('members', function (Blueprint $table) {
            $table->string('anh_hoi_vien')->nullable();  // Thêm trường 'anh_hoi_vien', kiểu string và có thể null
        });
    }
    
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('anh_hoi_vien');  // Xóa trường 'anh_hoi_vien' nếu rollback migration
        });
    }

};
