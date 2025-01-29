<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslatorAvailability extends Model
{
    use HasFactory;

    protected $fillable = ['translator_id', 'day', 'start_time', 'end_time', 'is_enabled','slot_duration'];

    public function translator()
    {
        return $this->belongsTo(User::class, 'translator_id');
    }
}
