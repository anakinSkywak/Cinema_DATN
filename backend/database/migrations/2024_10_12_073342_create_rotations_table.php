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
        Schema::create('rotations', function (Blueprint $table) {
            $table->id();
            $table->string('ten_phan_thuong',255);
            $table->decimal('muc_giam_gia' , 12,3)->nullable(); // gia cho ve giam gia theo muc tien 10k 20k
            $table->string('mo_ta',255);
            $table->integer('xac_xuat'); // xac xuat bao nhieu % de lam quay nhan phan thuong
            $table->integer('so_luong');
            $table->integer('so_luong_con_lai')->nullable();
            $table->tinyInteger('trang_thai')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotations');
    }
};
