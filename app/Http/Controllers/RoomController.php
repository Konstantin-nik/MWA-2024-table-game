<?php

namespace App\Http\Controllers;

use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::public()->toJoin()->get();

        return view('rooms.index', compact('rooms'));
    }
}
