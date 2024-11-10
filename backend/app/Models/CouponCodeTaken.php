<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCodeTaken extends Model
{
    use HasFactory;

    protected $fillable = [
        'countdownvoucher_id',
        'user_id',
    ];

    // Mối quan hệ với bảng 'countdown_vouchers'
    public function countdownvoucher()
    {
        return $this->belongsTo(CountdownVoucher::class, 'countdownvoucher_id');
    }
}
