<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    /**
     * Các thuộc tính có thể điền vào (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'noidung', // Nội dung liên hệ
        'user_id', // Khóa ngoại liên kết với user
    ];

    /**
     * Liên kết với model User (One-to-Many - Mỗi liên hệ thuộc về một user).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
