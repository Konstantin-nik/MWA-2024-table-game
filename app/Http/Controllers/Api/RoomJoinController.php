<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Rules\InvitationTokenIsValid;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoomJoinController extends Controller
{
    /**
     * Join a room by ID.
     */
    public function join(string $id, Request $request): JsonResponse
    {
        $this->isAuthorizedToJoin();
        $user = auth()->user();
        $room = Room::findOrFail($id);

        if ($user->canJoinRoom($room)) {
            $room->users()->attach($user);
            return response()->json(['message' => 'Successfully joined the room.'], 200);
        } else {
            return response()->json(['message' => 'Unauthorized to join this room.'], 401);
        }
    }

    /**
     * Join a room by invitation token.
     */
    public function joinByToken(Request $request): JsonResponse
    {
        $request->validate([
            'invitation_token' => ['required', new InvitationTokenIsValid],
        ], [
            'invitation_token.required' => 'The invitation token is required.',
        ]);

        $this->isAuthorizedToJoin();
        $room = Room::toJoin()->where('invitation_token', $request->invitation_token)->firstOrFail();

        $room->users()->attach(auth()->user());

        return response()->json(['message' => 'Successfully joined the room using the invitation token.'], 200);
    }

    /**
     * Leave a room by ID.
     */
    public function leave(string $id, Request $request): JsonResponse
    {
        $user = auth()->user();
        $room = Room::toJoin()->findOrFail($id);

        if ($user->canLeaveRoom($room)) {
            $room->users()->detach($user);
            return response()->json(['message' => 'Successfully left the room.'], 200);
        } else {
            return response()->json(['message' => 'Unauthorized to leave this room.'], 401);
        }
    }

    /**
     * Check if the user is authorized to join a room.
     */
    private function isAuthorizedToJoin(): void
    {
        $user = auth()->user();
        if ($user->isInAnyRoom()) {
            abort(401, 'You are already in a room.');
        }
    }
}