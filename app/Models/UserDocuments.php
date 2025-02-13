<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocuments extends Model
{

    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPathAttribute($value)
    {
        if ($value) {
            // Prepend your AWS S3 bucket URL to the profile_pic path
            return env('AWS_PATH') . $value;
        }
        return null;
    }
}
