<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeBlog extends Model
{
    use HasFactory;

    // Tên bảng
    protected $table = 'type_blogs';

    // Các thuộc tính có thể được mass assign
    protected $fillable = ['ten_loai_bai_viet'];

    // Quan hệ với bảng blogs
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'loaibaiviet_id');
    }
}
