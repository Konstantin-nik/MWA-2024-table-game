<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    // Model Relations --------------------------------------------------------
    public function users()
    {
        return $this->belongsToMany(User::class, 'participations');
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Model scopes -----------------------------------------------------------
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFinished($query)
    {
        return $query->whereNotNull('finished_at');
    }

    public function scopeToJoin($query)
    {
        return $query->whereNull('started_at');
    }

    // Model static functions -------------------------------------------------
    public static function generateUniqueInvitationToken()
    {
        do {
            $token = Str::random(10);
        } while (Room::toJoin()->where('invitation_token', $token)->exists());

        return $token;
    }

    // Model functions --------------------------------------------------------
    public function isFull()
    {
        return $this->capacity <= count($this->users);
    }

    public function isNotFull()
    {
        return ! $this->isFull();
    }

    public function isOpenToJoin()
    {
        return ! $this->started_at && ! $this->finished_at && $this->is_public;
    }

    public function isOpenToLeave()
    {
        return ! $this->started_at && ! $this->finished_at;
    }
}
