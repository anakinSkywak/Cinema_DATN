<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phim_id',
        'anh_khoang_khac',
        'noi_dung',
        'like',
        'dislike',
    ];

    // Quan hệ với comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Quan hệ với users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với movies
    public function movie()
    {
        return $this->belongsTo(Movie::class, 'phim_id');
    }
}
