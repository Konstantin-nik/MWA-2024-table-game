<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipationFactory> */
    use HasFactory;

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function session() 
    {
        return $this->belongsTo(Session::class);
    }
}
