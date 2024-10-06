<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History_rotations extends Model
{
    use HasFactory;

    protected $table = 'history_rotations';

    protected $fillable = [
        'vongquay_id',
        'user_id',
        'ngay_quay',
        'ket_qua',
        'trang_thai',
    ];
}
