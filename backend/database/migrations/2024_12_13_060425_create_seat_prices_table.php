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
        Schema::create('seat_prices', function (Blueprint $table) {
            // loai_ghe , ngay_trong_tuan = null or tên thứ , ngay_cu_the = null or ngày cụ thể 
            //, gio_bat_dau , gio_ket_thuc , gia_ghe , ten_ngay_le = null or tên ngày , ngay_le = true of fales
            $table->id();
            $table->string('loai_ghe', 255);
            $table->enum('thu_trong_tuan', [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday'
            ])->nullable();
            $table->date('ngay_cu_the')->nullable();
            $table->time('gio_bat_dau');
            $table->time('gio_ket_thuc');
            $table->decimal('gia_ghe', 12, 0);
            $table->string('ten_ngay_le', 255)->nullable();
            $table->boolean('la_ngay_le')->default(false);  //  fales / true
            $table->tinyInteger('trang_thai')->default(0);
            $table->softDeletes('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_prices');
    }
};
