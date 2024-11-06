<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'booking_id',
        'tong_tien',
        'tien_te',
        'phuong_thuc_thanh_toan',
        'ma_thanh_toan',
        'ma_tham_chieu',
        'ngay_thanh_toan',
        'trang_thai',
        'chi_tiet_giao_dich',
        'registermember_id'
    ];

    protected $dates = ['deleted_at'];

    public function booking()
    {
        return $this->belongsTo(Booking::class); // tao moi quan he voi booking 
    }


    ////////////////
    public function registerMember()
    {
        return $this->belongsTo(RegisterMember::class, 'register_member_id'); // ?? register_member_id
    }
}
