<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';
    protected $fillable = [
        'ho_ten',
        'anh',
        'gioi_tinh',
        'email',
        'so_dien_thoai',
        'password',
        'role',
        'diem_thuong',
        'ma_giam_gia',
        'so_luot_quay',
        'quyen_han'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // methods JWT tra ve token khi dang nhap
    //  xác thực JWT JSON Web Token trong Laravel khi sử dụng gói jwt-auth

    //getJWTIdentifier() sẽ lấy giá trị của khóa chính id của người dùng và đưa nó vào token
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // trả về giá trị của khóa chính của bản ghi hiện tại id 
    public function getJWTCustomClaims()
    {
        // return [];
        return ['role' => $this->role]; 
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // bam mk
    ];
}
