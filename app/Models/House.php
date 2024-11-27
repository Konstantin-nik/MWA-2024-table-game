<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $fillable = ['row_id', 'has_pool', 'position', 'number'];

    // Model Relations --------------------------------------------------------
    public function row()
    {
        return $this->belongsTo(Row::class);
    }
}
