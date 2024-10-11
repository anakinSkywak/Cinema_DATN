<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeBlog extends Model
{
    use HasFactory;

    protected $fillable = ['ten_loai_bai_viet'];
}
