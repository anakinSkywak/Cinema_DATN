<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponCodeTaken extends Model
{
    use HasFactory;

    protected $fillable = ['countdownvoucher_id', 'user_id', 'ngay_nhan', 'ngay_het_han','trang_thai'];

    // Liên kết với bảng CountdownVoucher
    public function countdownVoucher()
    {
        return $this->belongsTo(CountdownVoucher::class, 'countdownvoucher_id');
    }

    // Liên kết với bảng User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Tính toán ngày hết hạn (7 ngày sau ngày nhận)
    public static function boot()
    {
        parent::boot();
        // static::creating là một sự kiện được kích hoạt khi một mục mới được tạo trong cơ sở dữ liệu.
        static::creating(function ($coupon) {
            // Nếu không có ngày nhận thì gán ngày nhận là ngày hiện tại
            if (!$coupon->ngay_nhan) {
                $coupon->ngay_nhan = Carbon::today(); // Ngày nhận là hôm nay
            }

            // Tính toán ngày hết hạn là 7 ngày sau ngày nhận
            $coupon->ngay_het_han = Carbon::parse($coupon->ngay_nhan)->addDays(7);
        });
    }
}