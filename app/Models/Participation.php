<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipationFactory> */
    use HasFactory;

    // Model Relations ------------------------------------------------------
    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function contest() 
    {
        return $this->belongsTo(Contest::class);
    }
}
