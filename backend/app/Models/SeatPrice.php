<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeatPrice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'seat_prices';

    protected $fillable = [
        'loai_ghe',
        'thu_trong_tuan',
        'ngay_cu_the',
        'gio_bat_dau',
        'gio_ket_thuc',
        'gia_ghe',
        'ten_ngay_le',
        'la_ngay_le',
        'trang_thai'
    ];
    
    protected $dates = ['deleted_at']; //xoa softdelete

    // moi quan he
}
