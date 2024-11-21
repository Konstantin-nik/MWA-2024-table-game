<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Model Relations ------------------------------------------------------
    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'participations');
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function ownedRooms()
    {
        return $this->hasMany(Room::class, 'owner_id');
    }

    // Model Functions ------------------------------------------------------
    public function isInAnyRoom()
    {
        return $this->rooms()->whereNull('finished_at')->get()->isNotEmpty();
    }

    public function isNotInAnyRoom()
    {
        return ! $this->isInAnyRoom();
    }

    public function canJoinRoom(Room $room)
    {
        return $room->isOpenToJoin() && $this->isNotInAnyRoom() && $room->isNotFull();
    }

    public function isInRoom(Room $room)
    {
        return $room->users->contains($this);
    }
}
