<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingDetail extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'ghengoi_id',
        'trang_thai'
    ];

    protected $dates = ['deleted_at'];

    // dinh nghia moi quan he


    public function booking()
    {
        return $this->belongsTo(Booking::class); // moi quan hej nguoc lai tro den booking
    }

    // thiet lap moi quan he voi seats qua ngengoi_id de lay ghe ngoi khi booking
    public function seat()
    {
        return $this->belongsTo(Seat::class, 'ghengoi_id');
    }
}
