<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipationFactory> */
    use HasFactory;
 
    protected $guarded;

    protected $casts = [
        'scores' => 'array',
    ];

    // Model Relations ------------------------------------------------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function board()
    {
        return $this->hasOne(Board::class);
    }
}
