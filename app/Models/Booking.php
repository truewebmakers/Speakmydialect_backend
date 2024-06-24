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
        return $this->belongsTo(User::class,'translator_id','id');
    }

    public function client()
    {
        return $this->belongsTo(User::class,'client_id','id');
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
