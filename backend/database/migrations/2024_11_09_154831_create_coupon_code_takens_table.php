<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCodeTakensTable extends Migration
{
    public function up()
    {
        Schema::create('coupon_code_takens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('countdownvoucher_id')->constrained('countdown_vouchers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coupon_code_takens');
    }
}

