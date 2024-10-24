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
        'trang_thai',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'magiamgia_id');
    }
}
