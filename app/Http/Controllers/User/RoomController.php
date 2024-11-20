<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Str;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::public()->toJoin()->get();

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
            'invitation_token' => Str::random(10),
        ]);

        return redirect()->route('user.rooms.show', $room);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room = Room::findOrFail($id);

        return view('user.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $room = Room::findOrFail($id);
        $this->isAuthorized($room);

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
        $this->isAuthorized($room);

        $room->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'is_public' => $request->has('is_public'),
            'invitation_token' => Str::random(10),
        ]);

        session()->flash('success', 'Room Edited');

        return redirect()->route('user.rooms.show', $room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function isAuthorized(Room $room)
    {
        $user = auth()->user();
        if (! ($user->id == $room->owner_id)) {
            abort(401);
        }
    }
}
