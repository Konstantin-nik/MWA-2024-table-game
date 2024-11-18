<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;


    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // Model Relations ------------------------------------------------------
    public function users() 
    {
        return $this->belongsToMany(User::class, "participations");
    }

    public function rounds() 
    {
        return $this->hasMany(Round::class);
    }

    // Model scopes ---------------------------------------------------------
    public function scopePublic($query) 
    {
        return $query->where("is_public", true);
    }

    public function scopeFinished($query) 
    {
        return $query->whereNotNull('finished_at');
    }

    public function scopeToJoin($query) 
    {
        return $query->whereNull('started_at');
    }
}
