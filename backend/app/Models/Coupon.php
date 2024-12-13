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
        'Giam_max', // Sửa 'Giam_max' thành 'giam_max' để tuân thủ quy tắc đặt tên biến
        'mota',
        'so_luong',
        'so_luong_da_su_dung',
        'so_luong_con_lai', // Thêm trường 'so_luong_con_lai' để theo dõi số lượng còn lại
        'trang_thai',
    ];

    // Các trường kiểu ngày tháng
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Hook được gọi trước khi tạo dữ liệu
    protected static function boot()
    {
        parent::boot();

        // Khi tạo dữ liệu mới, trường so_luong_con_lai sẽ nhận giá trị của so_luong
        static::creating(function ($coupon) {
            $coupon->so_luong_con_lai = $coupon->so_luong;
        });
    }
}