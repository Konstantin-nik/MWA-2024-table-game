<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class House extends Model
{
    // Model Relations --------------------------------------------------------
    public function board()
    {
        return $this->BelongsTo(Board::class);    
    }
}
