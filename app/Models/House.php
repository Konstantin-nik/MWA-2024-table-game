<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_pool_constructed' => 'boolean',
        'has_pool' => 'boolean',
    ];

    // Model Relations --------------------------------------------------------
    public function row()
    {
        return $this->belongsTo(Row::class);
    }
}
