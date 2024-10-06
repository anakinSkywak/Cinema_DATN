<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $table = 'movies';

    protected $fillable = [
        'ten_phim',
        'anh_phim',
        'dao_dien',
        'dien_vien',
        'noi_dung',
        'trailer',
        'gia_ve',
        'danh_gia',
        'loaiphim_id',
    ];
}
