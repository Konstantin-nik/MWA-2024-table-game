<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    /** @use HasFactory<\Database\Factories\RoundFactory> */
    use HasFactory;

    protected $casts = [
        'finished_at' => 'datetime',
    ];

    // Model Relations ------------------------------------------------------
    public function contest() 
    {
        return $this->belongsTo(Contest::class);
    }

    public function actions() 
    {
        return $this->belongsToMany(Action::class,"action_round");
    }
}
