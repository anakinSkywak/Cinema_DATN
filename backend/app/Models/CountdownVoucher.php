<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountdownVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'magiamgia_id',
        'ngay',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'so_luong',
        'so_luong_con_lai',
        'trang_thai',
    ];

    // Sử dụng booted để xử lý sự kiện tạo đối tượng
    protected static function booted()
    {
        // Thêm sự kiện khi tạo mới CountdownVoucher
        static::creating(function ($countdownVoucher) {
            // Nếu số lượng còn lại chưa được xác định, gán nó bằng số lượng
            if (is_null($countdownVoucher->so_luong_con_lai)) {
                $countdownVoucher->so_luong_con_lai = $countdownVoucher->so_luong;
            }
        });
    }

    // Quan hệ với Coupon, mỗi CountdownVoucher thuộc về một Coupon
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'magiamgia_id');
    }

    // Quan hệ với CouponCodeTaken, mỗi CountdownVoucher có nhiều CouponCodeTaken
    public function couponCodeTakens()
    {
        return $this->hasMany(CouponCodeTaken::class, 'countdownvoucher_id');
    }
}
