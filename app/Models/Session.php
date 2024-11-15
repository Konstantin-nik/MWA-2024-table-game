<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    /** @use HasFactory<\Database\Factories\SessionFactory> */
    use HasFactory;

    public function user() {
        return $this->belongsToMany(User::class, "participations");
    }

    public function round() {
        return $this->hasMany(Round::class);
    }
}
