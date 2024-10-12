<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = ['dangkyhoivien_id', 'ngay_dang_ky', 'trang_thai'];

    public function registerMember()
    {
        return $this->belongsTo(RegisterMember::class, 'dangkyhoivien_id');
    }
}
