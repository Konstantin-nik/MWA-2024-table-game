<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomJoinController extends Controller
{
    public function join(string $id, Request $request)
    {
        $this->isAuthorizedToJoin();
        $user = auth()->user();
        $room = Room::findOrFail($id);

        if ($user->canJoinRoom($room) || $room->invitation_token == $request->invitation_token) {
            $room->users()->attach(auth()->user());
        } else {
            abort(401);
        }

        return redirect()->route('user.rooms.show', $id);
    }

    private function isAuthorizedToJoin()
    {
        $user = auth()->user();
        if ($user->isInRoom()) {
            abort(401);
        }
    }
}
