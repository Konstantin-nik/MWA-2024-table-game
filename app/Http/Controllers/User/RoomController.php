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
        $rooms = Room::all();

        return view("user.rooms.index", compact("rooms"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("user.rooms.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"=> ["string", "min:4", "max:25"],
            "capacity"=>["integer", "min:2", "max:8"],
        ]);

        $room = Room::create([
            "name"=> $request->name,
            "capacity"=> $request->capacity,
            "is_public"=> $request->is_public,
            "invitation_token"=> Str::random(10),
        ]);

        return redirect()->route("user.rooms.show", $room)->with("success","Room Created");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room = Room::find($id);

        return view("user.rooms.show", compact("room"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
