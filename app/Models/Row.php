<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    protected $guarded = [];

    protected $casts = [
        'landscape_values' => 'array',
    ];

    // Model Relations --------------------------------------------------------
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function houses()
    {
        return $this->hasMany(House::class);
    }

    public function fences()
    {
        return $this->hasMany(Fence::class);
    }
}
