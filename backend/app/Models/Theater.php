<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Theater extends Model
{
    use HasFactory;
    use SoftDeletes; // xoa api  soft delete
    protected $table = 'theaters';
    
    protected $fillable = [
        'ten_rap',
        'dia_diem',
        'tong_ghe',
    ];
    protected $dates = ['deleted_at'];
}
