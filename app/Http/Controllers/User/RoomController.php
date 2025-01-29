<?php

namespace App\Http\Controllers\User;

use App\Events\GameStarted;
use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Deck;
use App\Models\Participation;
use App\Models\Room;
use DB;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::public()->toJoin()->get();

        $rooms->loadCount('users');

        return view('user.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.rooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['string', 'min:4', 'max:25'],
            'capacity' => ['integer', 'min:2', 'max:8'],
        ]);

        $room = Room::create([
            'name' => $request->name,
            'owner_id' => auth()->user()->id,
            'capacity' => $request->capacity,
            'is_public' => $request->has('is_public'),
            'invitation_token' => Room::generateUniqueInvitationToken(),
        ]);

        if (auth()->user()->canJoinRoom($room)) {
            Participation::create([
                'user_id' => auth()->user()->id,
                'room_id' => $room->id,
            ]);
        }

        session()->flash('success', 'Room Created');

        return redirect()->route('user.rooms.show', $room);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('user.rooms.show', ['roomId' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $room = Room::findOrFail($id);
        $this->isAuthorizedToEdit($room);

        return view('user.rooms.edit', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['string', 'min:4', 'max:25'],
            'capacity' => ['integer', 'min:2', 'max:8'],
        ]);

        $room = Room::findOrFail($id);
        $this->isAuthorizedToEdit($room);

        $room->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'is_public' => $request->has('is_public'),
        ]);

        session()->flash('success', 'Room Edited');

        return redirect()->route('user.rooms.show', $room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);
        $this->isAuthorizedToDelete($room);
        $room->delete();

        session()->flash('success', 'Room was Deleted!');

        return redirect()->route('user.rooms.index');
    }

    /**
     * Display a listing of owned rooms.
     */
    public function ownedRooms()
    {
        $user = auth()->user();
        $rooms = $user->ownedRooms()->orderBy('started_at')->get();

        $rooms->loadCount('users');

        return view('user.rooms.owned_rooms', compact('rooms'));
    }

    /**
     * Start Game for this room.
     */
    public function start(string $id)
    {
        $room = Room::findOrFail($id);
        $this->isAuthorizedToStart($room);
        DB::transaction(function () use ($room) {
            Deck::createDecksForRoom($room->id, 0);

            // Initializing Boards for each player
            foreach ($room->participations as $participation) {
                Board::initializeBoard($participation);
            }

            // Creating initial round
            $room->rounds()->create([
                'index' => 0,
            ]);

            $room->update([
                'started_at' => now(),
            ]);
        });

        broadcast(new GameStarted($room->id));

        return redirect()->route('user.rooms.show', $room)->with('success', 'Game started successfully!');
    }

    // Authorization functions ------------------------------------------------
    private function isAuthorizedToEdit(Room $room)
    {
        $user = auth()->user();
        if (! ($user->canEditRoom($room))) {
            abort(401);
        }
    }

    private function isAuthorizedToDelete(Room $room)
    {
        $user = auth()->user();
        if (! ($user->canDeleteRoom($room))) {
            abort(401);
        }
    }

    private function isAuthorizedToStart(Room $room)
    {
        $user = auth()->user();
        if (! ($user->canStartRoom($room))) {
            abort(401);
        }
    }
}
