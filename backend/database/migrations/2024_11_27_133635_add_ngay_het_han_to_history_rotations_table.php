<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('history_rotations', function (Blueprint $table) {
            $table->dateTime('ngay_het_han')->nullable()->after('ngay_quay'); 
        });
    }

    public function down()
    {
        Schema::table('history_rotations', function (Blueprint $table) {
            $table->dropColumn('ngay_het_han');
        });
    }
};
