<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterMember extends Model
{
    use HasFactory;

    // Các trường có thể điền vào một cách an toàn
    protected $fillable = [
        'user_id',
        'hoivien_id',
        'tong_tien',
        'ngay_dang_ky',
        'ngay_het_han', // Thêm trường ngày hết hạn vào mảng fillable
        'trang_thai',
    ];

    // Quan hệ với User
    public function user()  
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với Member
    public function member()
    {
        return $this->belongsTo(Member::class, 'hoivien_id');
    }

    // Quan hệ với Membership
    public function memberships()
    {
        return $this->hasMany(Membership::class); // Đảm bảo có quan hệ với class Membership
    }

    // Quan hệ với Payment
    public function payments()
    {
        return $this->hasMany(Payment::class, 'registermember_id', 'id');
    }    
}