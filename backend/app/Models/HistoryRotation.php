<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryRotation extends Model
{
    use HasFactory;

    protected $table = 'history_rotations';

    protected $fillable = [
        'user_id', 'vongquay_id', 'ket_qua', 'ngay_quay', 'trang_thai','ngay_het_han','dieu_kien'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function rotation()
    {
        return $this->belongsTo(Rotation::class, 'vongquay_id');
    }
}
