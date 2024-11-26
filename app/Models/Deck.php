<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    // Model Relations ------------------------------------------------------
    public function cards()
    {
        return $this->hasMany(Card::class);    
    }
}
