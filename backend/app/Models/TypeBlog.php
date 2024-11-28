<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeBlog extends Model
{
    use HasFactory, SoftDeletes;

    // Khai báo bảng trong database nếu tên bảng khác với tên model
    protected $table = 'type_blogs';

    // Các trường có thể gán giá trị hàng loạt
    protected $fillable = [
        'ten_loai_bai_viet', 
        'anh', 
        'ngay'
    ];

    // Các trường sẽ được tự động chuyển đổi kiểu
    protected $casts = [
        'ngay' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
