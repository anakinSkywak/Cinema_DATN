<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    // Giữ nguyên định nghĩa mảng fillable, đã hợp nhất các phần trùng lặp
    protected $fillable = [
        'loai_hoi_vien',
        'uu_dai',
        'thoi_gian',
        'ghi_chu',
        'gia',
        'trang_thai',
        'anh_hoi_vien', 
    ];

    // Giữ lại phương thức registrations và cập nhật cho phù hợp với tên class
    public function registrations()
    {
        return $this->hasMany(RegisterMember::class, 'member_id');
    }
}