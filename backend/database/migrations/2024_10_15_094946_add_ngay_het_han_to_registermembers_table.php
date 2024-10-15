<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNgayHetHanToRegistermembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_members', function (Blueprint $table) {
            $table->date('ngay_het_han')->nullable()->after('ngay_dang_ky'); // Thêm cột ngày hết hạn sau cột ngay_dang_ky
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_members', function (Blueprint $table) {
            $table->dropColumn('ngay_het_han');
        });
    }
}
