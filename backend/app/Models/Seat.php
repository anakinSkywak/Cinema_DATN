<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $table = 'seats';

    protected $fillable = [
        'so_ghe_ngoi',
        'loai_ghe_ngoi',
        'room_id',
    ];
}
