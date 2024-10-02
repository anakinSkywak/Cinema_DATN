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
        Schema::create('history_rotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vongquay_id')->references('id')->on('rotations');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->date('ngay_quay');
            $table->string('ket_qua' , 255);
            $table->tinyInteger('trang_thai')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_rotations');
    }
};
