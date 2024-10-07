<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\AddSoftDeletesToTable; // impot file trait dat tao de su dung them softdelete all

return new class extends Migration
{
    use AddSoftDeletesToTable;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm cột deleted_at cho bảng users
        Schema::table('users', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });

        // Thêm cột deleted_at cho bảng bookings
        Schema::table('bookings', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });

        // Thêm cột deleted_at cho bảng blogs
        Schema::table('blogs', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });

        // Thêm cột deleted_at cho bảng booking_details
        Schema::table('booking_details', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });

        // Thêm cột deleted_at cho bảng comments
        Schema::table('comments', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });

        // Thêm cột deleted_at cho bảng contacts
        Schema::table('contacts', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng countdown_vouchers
        Schema::table('countdown_vouchers', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng foods
        Schema::table('foods', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng history_rotations
        Schema::table('history_rotations', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng members
        Schema::table('members', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng memberships
        Schema::table('memberships', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng moments
        Schema::table('moments', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng movies
        Schema::table('movies', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng movie_genres
        Schema::table('movie_genres', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng payments
        Schema::table('payments', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng playlist_movies
        Schema::table('playlist_movies', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng register_members
        Schema::table('register_members', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng rooms
        Schema::table('rooms', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng rotations
        Schema::table('rotations', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng seats
        Schema::table('seats', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng showtimes
        Schema::table('showtimes', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng theaters
        Schema::table('theaters', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
        // Thêm cột deleted_at cho bảng type_blogs
        Schema::table('type_blogs', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });
       
        // Thêm cột deleted_at cho bảng vouchers
        Schema::table('vouchers', function (Blueprint $table) {
            $this->addSoftDeletes($table);
        });



    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multiple_tables', function (Blueprint $table) {
            //
        });
    }
};
