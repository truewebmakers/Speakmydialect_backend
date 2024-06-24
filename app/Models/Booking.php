<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Booking extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function translator()
    {
        return $this->belongsTo(User::class,'translator_id','id')->select('translator_id','uuid', 'fname', 'lname','email','user_type'); // Replace 'id', 'name', 'email' with the columns you need;
    }
    public function translatorMeta()
    {
        return $this->belongsTo(UserMeta::class,'translator_id','user_id')->select('user_id','phone', 'profile_pic', 'intro','gender'); // Replace 'id', 'name', 'email' with the columns you need;
    }

    public function client()
    {
        return $this->belongsTo(User::class,'client_id','id')->select('client_id','uuid', 'fname', 'lname','email','user_type');
    }

    public function clientMeta()
    {
        return $this->belongsTo(UserMeta::class,'client_id','user_id')->select('user_id','phone', 'profile_pic', 'intro','gender');
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
