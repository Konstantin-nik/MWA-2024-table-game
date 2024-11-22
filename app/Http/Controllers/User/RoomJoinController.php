<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Rules\InvitationTokenIsValid;
use Illuminate\Http\Request;

class RoomJoinController extends Controller
{
    public function join(string $id, Request $request)
    {
        $this->isAuthorizedToJoin();
        $user = auth()->user();
        $room = Room::findOrFail($id);

        if ($user->canJoinRoom($room)) {
            $room->users()->attach(auth()->user());
        } else {
            abort(401);
        }

        return redirect()->route('user.rooms.show', $id);
    }

    public function joinByToken(Request $request)
    {
        $request->validate([
            'invitation_token' => ['required', new InvitationTokenIsValid],
        ], [
            'invitation_token.required' => 'The invitation token is required.',
        ]);

        $this->isAuthorizedToJoin();
        $room = Room::toJoin()->where('invitation_token', $request->invitation_token)->firstOrFail();

        $room->users()->attach(auth()->user());

        return redirect()->route('user.rooms.show', $room->id);
    }

    public function leave(string $id, Request $request)
    {
        $user = auth()->user();
        $room = Room::toJoin()->findOrFail($id);

        if ($user->canLeaveRoom($room)) {
            $room->users()->detach($user);
        }

        return redirect()->route('user.rooms.show', $id);
    }

    private function isAuthorizedToJoin()
    {
        $user = auth()->user();
        if ($user->isInAnyRoom()) {
            abort(401);
        }
    }
}
