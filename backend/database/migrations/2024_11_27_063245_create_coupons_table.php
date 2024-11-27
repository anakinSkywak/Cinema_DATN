<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id(); // id Chính
            $table->string('ma_giam_gia'); // mã giảm giá
            $table->integer('muc_giam_gia'); // mức giảm giá
            $table->decimal('gia_don_toi_thieu', 15, 0)->nullable(); // giá đơn tối thiểu để áp dụng mã giảm giá
            $table->string('mota'); // mô tả
            $table->integer('so_luong'); // số lượng
            $table->integer('so_luong_da_su_dung')->nullable(); // số lượng đã sử dụng
            $table->tinyInteger('trang_thai')->default(0); // trạng thái
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
