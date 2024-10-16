<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'movies';

    protected $fillable = [
        'ten_phim',
        'anh_phim',
        'dao_dien',
        'dien_vien',
        'noi_dung',
        'trailer',
        'gia_ve',
        'hinh_thuc_phim'
    ];

    protected $dates = ['deleted_at'];

    // thiet lap quan he nhieu nhieu voi bang the loai phim
    // public function movie_genres()
    // { // trung gian luu tru phim va nhieu the loai phim
    //     return $this->belongsToMany(MovieGenre::class, 'movie_movie_genre'); // trung gian luu tru phim va nhieu the loai phim
    // }

    // public function storage_movie_genres()
    // {
    //     return $this->belongsToMany(MovieGenre::class, 'movie_genre_movie', 'movie_id', 'movie_genre_id');
    // }

    public function movie_genres()
    {
        return $this->belongsToMany(MovieGenre::class, 'movie_movie_genre', 'movie_id', 'movie_genre_id');
    }
}
