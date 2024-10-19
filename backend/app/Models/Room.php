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
    public function seats(){
        return  $this->hasMany(Seat::class , 'room_id');
    }

    // phương thức tạo ghế ngồi tự động 
    // public function addCreate($so_luong_ghe = 10){  // mac dinh moi phong deu co 150 ghe tieu chuan
    //     $seats = []; // mảng rỗng chứa ghế ngồi

    //     // tạo ghế ngồi tự động với for lặp tạo all 
    //     for($i = 1 ; $i <= $so_luong_ghe ; $i++){
    //         $seats[] = [
    //             'so_ghe_ngoi' => 'A' . $i, // A1 -> 150
    //             'loai_ghe_ngoi' => 'Thường',
    //             'room_id' => $this->id,
    //             'gia_ghe' => 10, // 10 đ 10 nghìn
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ];
    //     }

    //     // thêm all ghế ngoi cung 1 luc
    //     Seat::insert($seats);

    // }
}
