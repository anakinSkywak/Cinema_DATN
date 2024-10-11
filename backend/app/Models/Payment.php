<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'booking_id',
        'tong_tien',
        'phuong_thuc_thanh_toan',
        'ma_thanh_toan',
        'ngay_thanh_toan',
        'trang_thai'
    ];

    protected $dates = ['deleted_at'];

    public function booking()
    {
        return $this->belongsTo(Booking::class); // tao moi quan he voi booking 
    }
}
