<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Booking extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'duration' => 'array', // or 'json' depending on your version
    ];

    public function translator()
    {
        return $this->belongsTo(User::class,'translator_id','id')->select('id','uuid',  'country_code','phone_number','fname', 'lname','email','user_type');
    }
    public function translatorMeta()
    {
        return $this->belongsTo(UserMeta::class,'translator_id','user_id')->select('user_id', 'profile_pic', 'intro','gender');
    }

    public function client()
    {
        return $this->belongsTo(User::class,'client_id','id')->select('id','uuid', 'country_code','phone_number','fname', 'lname','email','user_type');
    }

    public function clientMeta()
    {
        return $this->belongsTo(UserMeta::class,'client_id','user_id')->select('user_id','phone', 'profile_pic', 'intro','gender','address');
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class,'job_id','id');
    }
    public function slots()
    {
        return $this->hasMany(BookingSlot::class,'booking_id','id');
    }


    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }
}
