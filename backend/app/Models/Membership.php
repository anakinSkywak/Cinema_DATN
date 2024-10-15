<?php

namespace App\Models;

use App\Models\RegisterMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = ['dangkyhoivien_id', 'ngay_dang_ky', 'trang_thai'];

    public function registerMember()
    {
        return $this->belongsTo(RegisterMember::class, 'dangkyhoivien_id');
    }
}
