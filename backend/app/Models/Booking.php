<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'thongtinchieu_id',
        'so_luong',
        'ghi_chu',
        'ma_giam_gia',
        'doan_id',
        'tong_tien',
        'tong_tien_thanh_toan',
        'ngay_mua',
        'trang_thai'
    ];

    protected $dates = ['deleted_at'];
}
