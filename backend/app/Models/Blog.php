<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    // Tên bảng
    protected $table = 'blogs';

    // Các thuộc tính có thể được mass assign
    protected $fillable = ['loaibaiviet_id', 'tieu_de', 'anh_bai_viet', 'noi_dung', 'ngay_viet'];

    // Quan hệ với bảng type_blogs
    public function typeBlog()
    {
        return $this->belongsTo(TypeBlog::class, 'loaibaiviet_id');
    }
}
