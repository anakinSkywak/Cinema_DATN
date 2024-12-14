<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
