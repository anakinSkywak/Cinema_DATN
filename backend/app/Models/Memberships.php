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
    ];

    // Mối quan hệ với RegisterMember
    public function registerMember()
    {
        return $this->belongsTo(RegisterMember::class, 'dangkyhoivien_id');
    }

    // // Định nghĩa phương thức truy cập (Accessor) cho status
    // public function getStatusAttribute()
    // {
    //     // Kiểm tra nếu ngày hết hạn < ngày hiện tại, thẻ hết hạn
    //     if ($this->ngay_het_han && Carbon::parse($this->ngay_het_han)->isPast()) {
    //         return 'expired';
    //     }
    //     return 'active';
    // }

    // // Sự kiện "saving" để tự động cập nhật status
    // public static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($model) {
    //         // Tự động cập nhật status trước khi lưu
    //         if ($model->ngay_het_han && Carbon::parse($model->ngay_het_han)->isPast()) {
    //             $model->status = 'expired';
    //         } else {
    //             $model->status = 'active';
    //         }
    //     });
    // }
}
