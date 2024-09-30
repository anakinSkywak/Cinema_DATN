<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTheatersTable extends Migration
{
    public function up()
    {
        Schema::create('theaters', function (Blueprint $table) {
            $table->id(); // Tạo cột id với auto_increment và là khóa chính
            $table->string('ten_rap'); // Tên rạp
            $table->string('dia_diem'); // Địa điểm
            $table->bigInteger('tong_ghe'); // Tổng ghế không có auto_increment
            $table->timestamps(); // Thêm cột created_at và updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('theaters');
    }
}

