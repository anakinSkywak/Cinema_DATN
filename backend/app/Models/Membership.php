<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MemberShip extends Model
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function boot()
    {
        parent::boot();

        // Hook vào sự kiện khi truy vấn model
        static::retrieved(function ($membership) {
            $currentDate = Carbon::now();
            $expirationDate = Carbon::parse($membership->ngay_het_han);

            if ($expirationDate->isBefore($currentDate)) {
                $membership->trang_thai = 1; // Đã hết hạn
                $membership->renewal_message = "Thẻ hội viên đã hết hạn.";
                $membership->save(); // Tự động cập nhật vào cơ sở dữ liệu
            }
        });
    }
    
}
