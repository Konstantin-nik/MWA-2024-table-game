<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    protected $fillable = ['board_id', 'index'];

    // Model Relations --------------------------------------------------------
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function houses()
    {
        return $this->hasMany(House::class);
    }
}
