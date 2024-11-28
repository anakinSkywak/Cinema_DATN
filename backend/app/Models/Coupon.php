<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    // Tên bảng trong cơ sở dữ liệu
    protected $table = 'coupons';

    // Các trường được phép gán dữ liệu
    protected $fillable = [
        'ma_giam_gia',
        'muc_giam_gia',
        'gia_don_toi_thieu',
        'mota',
        'so_luong',
        'so_luong_da_su_dung',
        'trang_thai',
    ];

    // Các trường kiểu ngày tháng
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
