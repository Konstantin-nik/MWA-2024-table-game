<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    // Model Relations --------------------------------------------------------
    public function houses()
    {
        return $this->hasMany(House::class);
    }
}
