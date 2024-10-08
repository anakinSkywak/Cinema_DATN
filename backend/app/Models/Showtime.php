<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showtime extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'showtimes';

    protected $fillable = [
        'ngay_chieu',
        'thoi_luong_chieu',
        'phim_id',
        'rapphim_id',
        'room_id',
        'gio_chieu',
    ];

    protected $dates = ['deleted_at'];

    // tham chieu moi quan he de truy van lay du lieu cac cot
    // all moi quan he 1 nhieu

    public function movie(){

        return $this->belongsTo(Movie::class , 'phim_id');
        
    }

    public function theater(){

        return $this->belongsTo(Theater::class , 'rapphim_id ');

    }

    public function room(){

        return $this->belongsTo(Room::class , 'room_id');

    }
}
