<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten_phan_thuong',
        'mota',
        'so_luong_con_lai',
        'xac_xuat',
        'trang_thai'
    ];
}
