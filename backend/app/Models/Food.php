<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Food extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'foods';

    protected $fillable = [
        'ten_do_an',
        'gia',
        'ghi_chu',
        'trang_thai'
    ];

    protected $dates = ['deleted_at'];
}
