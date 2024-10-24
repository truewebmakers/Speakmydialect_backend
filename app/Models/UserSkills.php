<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkills extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'language', 'level', 'status','dialect'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
