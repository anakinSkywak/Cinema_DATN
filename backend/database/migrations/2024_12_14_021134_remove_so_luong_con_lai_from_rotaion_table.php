<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSoLuongConLaiFromRotaionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rotations', function (Blueprint $table) {
            $table->dropColumn('so_luong_con_lai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rotations', function (Blueprint $table) {
            $table->integer('so_luong_con_lai')->nullable(); // Hoặc kiểu dữ liệu ban đầu của cột
        });
    }
}
