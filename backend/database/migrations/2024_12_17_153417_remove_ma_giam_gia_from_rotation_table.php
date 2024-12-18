<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rotations', function (Blueprint $table) {
            $table->dropColumn('muc_giam_gia');
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
            $table->string('muc_giam_gia')->nullable(); // Thêm lại cột khi rollback
        });
    }
};
