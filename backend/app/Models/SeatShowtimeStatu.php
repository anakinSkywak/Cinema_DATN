<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeatShowtimeStatu extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'seat_showtime_status';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'ghengoi_id',
        'thongtinchieu_id',
        'trang_thai'
    ];

    // 


}
