<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    // Model Relations ------------------------------------------------------
    public function deck()
    {
        return $this->belongsTo(Deck::class);    
    }
}
