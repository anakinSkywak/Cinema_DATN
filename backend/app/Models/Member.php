<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;


    protected $fillable = [
        'loai_hoi_vien',
        'uu_dai',
        'thoi_gian',
        'ghi_chu',
        'gia',
        'trang_thai',
    ];

    public function registrations()
    {
        return $this->hasMany(RegisterMember::class, 'member_id');
    }
}

    protected $fillable = ['loai_hoi_vien', 'uu_dai', 'thoi_gian', 'ghi_chu', 'gia', 'trang_thai'];

    public function registerMembers()
    {
        return $this->hasMany(RegisterMember::class);
    }
}

