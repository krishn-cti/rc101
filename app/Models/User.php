<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role_id',
        'google_classroom_role',
        'google_id',
        'google_token',
        'google_refresh_token',
        'google_profile_image',
        'default_address_id',
        'profile_image',
        'email',
        'number',
        'city',
        'state',
        'country',
        'website_link',
        'discord_name',
        'bot_id',
        'weight_class_id',
        'tournament',
        'email_verified_at',
        'show_password',
        'password',
        'designation',
        'about',
        'remember_token',
        'status',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function bot()
    {
        return $this->belongsTo(Bot::class, 'bot_id', 'id')->select(['id', 'name']);
    }

    public function allBots()
    {
        return $this->hasMany(Bot::class, 'created_by', 'id');
    }

    public function weightClass()
    {
        return $this->belongsTo(WeightClassCategory::class, 'weight_class_id', 'id')->select(['id', 'name']);
    }

    public function getProfileImageAttribute($value)
    {
        if ($value) {
            return asset('profile_images/' . $value);
        } else {
            return asset('admin/img/bg-img/no-user.webp');
        }
    }

    public function getNumberAttribute($value)
    {
        return $value ?? "#N/A";
    }

    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isStudent()
    {
        return $this->role_id === 2;
    }

    public function isTeacher()
    {
        return $this->role_id === 3;
    }

    public function isTeam()
    {
        return $this->role_id === 4;
    }
}
