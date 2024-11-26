<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;

class GameController extends Controller
{
    public function show(string $id)
    {
        $room = Room::findOrFail($id);
        $this->isAuthorizedToShowGame($room);

        return view('user.game');
    }

    public function isAuthorizedToShowGame(Room $room)
    {
        $user = auth()->user();
        if (! ($room->isStarted() && $room->isNotFinished() && $user->isInRoom($room)))
        {
            abort(401);
        }
    }
}
