<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten_phan_thuong',
        'muc_giam_gia',
        'mo_ta',
        'xac_xuat',
        'so_luong',
        'trang_thai',
    ];
}
