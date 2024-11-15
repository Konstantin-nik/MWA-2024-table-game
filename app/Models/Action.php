<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    /** @use HasFactory<\Database\Factories\ActionFactory> */
    use HasFactory;

    // Model Relations ------------------------------------------------------
    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function rounds() 
    {
        return $this->belongsToMany(Round::class,  "action_round");
    }
}
