<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('room.{roomId}', function (User $user, $roomId) {
    $room = Room::find($roomId);
    if ($room && $room->participations->contains('user_id', $user->id)) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    return false;
});
