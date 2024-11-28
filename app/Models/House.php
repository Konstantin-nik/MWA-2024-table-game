<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $guarded = [];

    // Model Relations --------------------------------------------------------
    public function row()
    {
        return $this->belongsTo(Row::class);
    }
}
