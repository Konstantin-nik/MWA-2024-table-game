<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function decks()
    {
        return $this->hasMany(Deck::class);
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
        if ($this->users_count !== null) {
            return $this->capacity <= $this->users_count;
        } else {
            return $this->capacity <= $this->users()->count();
        }
    }

    public function isNotFull()
    {
        return ! $this->isFull();
    }

    public function isFinished()
    {
        return $this->finished_at;
    }

    public function isNotFinished()
    {
        return ! $this->isFinished();
    }

    public function isStarted()
    {
        return $this->started_at;
    }

    public function isNotStarted()
    {
        return ! $this->isStarted();
    }

    public function isStartedOrFinished()
    {
        return $this->isStarted() || $this->isFinished();
    }

    public function isNotStartedOrFinished()
    {
        return ! $this->isStartedOrFinished();
    }

    public function isOpenToJoin()
    {
        return $this->isNotStartedOrFinished();
    }

    public function isOpenToLeave()
    {
        return $this->isNotStartedOrFinished();
    }

    public function canBeDeleted()
    {
        return $this->isNotStartedOrFinished();
    }

    public function canBeEdited()
    {
        return $this->isNotStartedOrFinished();
    }

    public function canBeStarted()
    {
        return $this->isNotStartedOrFinished() && $this->users()->count() >= 1;
    }

    public function hasUser(User $user)
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
