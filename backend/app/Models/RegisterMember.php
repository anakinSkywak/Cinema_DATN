<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hoivien_id',
        'tong_tien',
        'ngay_dang_ky',
        'trang_thai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'hoivien_id');
    }

    public function memberships()
    {
        return $this->hasMany(MemberShips::class, 'dangkyhoivien_id');
    }
}
