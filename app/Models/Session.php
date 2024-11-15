<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    /** @use HasFactory<\Database\Factories\SessionFactory> */
    use HasFactory;

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user() 
    {
        return $this->belongsToMany(User::class, "participations");
    }

    public function rounds() 
    {
        return $this->hasMany(Round::class);
    }

    // Model scopes -------
    public function scopePublic($query) 
    {
        return $query->where('public', '===', true);
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
