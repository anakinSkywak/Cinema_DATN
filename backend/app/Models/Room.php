<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'rooms';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'ten_phong_chieu',
        'tong_ghe_phong',
        'rapphim_id',
    ];
     
    // đĩnh nghĩa mỗi phòng chiếu một rạp phim 
    public function theater(){
        // xác định mối quan hệ giữa 2 bảng với nhau nhiều room có ở một theater
        return $this->belongsTo(Theater::class , 'rapphim_id');
    }

    //quan hệ 1-n một phòng chiếu có nhiều ghế ngồi
    public function seat(){
        return  $this->hasMany(Seat::class , 'room_id');
    }

    

}
