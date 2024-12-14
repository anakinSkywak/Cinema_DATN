<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveXacXuatFromRotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rotations', function (Blueprint $table) {
            $table->dropColumn('xac_xuat');
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
            $table->integer('xac_xuat')->nullable(); // Kiểu dữ liệu ban đầu của cột
        });
    }
}
