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

    // mối quan hệ 
    public function showtime()
    {
        return $this->belongsTo(Showtime::class, 'thongtinchieu_id'); // Khóa ngoại là thongtinchieu_id
    }

    // 1-  nhiều booking có nhieu chi tiet booking /
    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'booking_id');
    }

    // Mối quan hệ với bảng payments 1 booking có 1 thanh toán 
    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id');  // Khóa ngoại là booking_id
    }

    // 1-n 1 booking có nhiều food đồ ăn
    public function food()
    {
        return $this->belongsTo(Food::class, 'doan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
