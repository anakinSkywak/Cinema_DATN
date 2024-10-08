<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberShips extends Model
{
    use HasFactory;

    protected $fillable = [
        'register_member_id',
        'so_the',
        'ngay_cap',
        'ngay_het_han',
    ];

    public function registerMember()
    {
        return $this->belongsTo(RegisterMember::class, 'register_member_id');
    }
}
