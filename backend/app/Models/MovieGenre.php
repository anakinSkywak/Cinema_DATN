<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovieGenre extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'moviegenres';
    protected $fillable = [
        'ten_loai_phim',
    ];

    protected $dates = ['deleted_at'];

    // thiet lap quan he nhieu nhieu voi bang the loai phim
    public function movies(){ // trung gian luu tru phim va nhieu the loai phim
        return $this->belongsToMany(Movie::class , 'movie_movie_genre'); // trung gian luu tru phim va nhieu the loai phim
    }


}
