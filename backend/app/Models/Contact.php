<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['noidung', 'user_id'];

    // Khai báo quan hệ với bảng users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
