<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seat extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'seats';

    protected $fillable = [
        'so_ghe_ngoi',
        'loai_ghe_ngoi',
        'room_id',
    ];
    
    protected $dates = ['deleted_at']; //xoa softdelete

    // đinh nghĩa tham chiếu bảng chỗ ngồi quan hệ với room
    public function room(){
        return $this->belongsTo(Room::class , 'room_id');
    }
}
