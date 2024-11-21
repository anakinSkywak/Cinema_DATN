<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MemberShips extends Model
{
    use HasFactory;

    protected $table = 'memberships';

    protected $fillable = [
        'dangkyhoivien_id',
        'so_the',
        'ngay_dang_ky',
        'ngay_het_han',
        'trang_thai',
        'renewal_message'
    ];

    // Mối quan hệ với RegisterMember
    public function registerMember()
    {
        return $this->belongsTo(RegisterMember::class, 'dangkyhoivien_id');
    }

}
