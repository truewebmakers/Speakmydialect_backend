<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\CustomEmailVerification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'username',
        'email',
        'password',
        'user_type',
        'uuid',
        'status',
        'reason',
        'address',
        'country_code',
        'phone_number'
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomEmailVerification());
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    public function UserMeta(){
        return $this->hasOne(UserMeta::class, 'user_id', 'id');

    }
    // Define the one-to-many relationship with UserSkill
    public function userSkills()
    {
        return $this->hasMany(UserSkills::class);
    }

    public function UserEducation()
    {
        return $this->hasMany(UserEduction::class);
    }

    public function UserWorkExperince()
    {
        return $this->hasMany(UserWorkExperience::class);
    }
}
