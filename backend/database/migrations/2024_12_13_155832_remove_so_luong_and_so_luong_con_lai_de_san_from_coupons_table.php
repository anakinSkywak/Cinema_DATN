<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSoLuongAndSoLuongConLaiDeSanFromCouponsTable extends Migration
{
    /**
     * Chạy migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['so_luong', 'so_luong_con_lai_de_san']);
        });
    }

    /**
     * Hoàn tác migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('so_luong')->nullable(false);
            $table->unsignedInteger('so_luong_con_lai_de_san')->default(0);
        });
    }
}
