<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id(); // Tạo cột id với auto_increment và là khóa chính
            $table->string('ten_phong_chieu'); // Tên phòng chiếu
            $table->bigInteger('tong_ghe_phong'); // Tổng ghế phòng không có auto_increment

            $table->unsignedBigInteger('rapphim_id');
            $table->foreign('rapphim_id')->references('id')->on('theaters'); // Khóa ngoại
            $table->timestamps(); // Thêm cột created_at và updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}

