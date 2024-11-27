<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    // Model Relations --------------------------------------------------------
    public function board()
    {
        return $this->BelongsTo(Board::class);
    }
}
