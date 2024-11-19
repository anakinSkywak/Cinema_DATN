<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCodeTaken extends Model
{
    use HasFactory;

    protected $fillable = ['countdownvoucher_id', 'user_id'];

    // Liên kết với bảng CountdownVoucher
    public function countdownVoucher()
    {
        return $this->belongsTo(CountdownVoucher::class, 'countdownvoucher_id');
    }

    // Liên kết với bảng User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}