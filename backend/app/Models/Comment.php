<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'movie_id',
        'moment_id',
        'content',
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với Movie
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    // Quan hệ với Moment
    public function moment()
    {
        return $this->belongsTo(Moment::class);
    }
}
