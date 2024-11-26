<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;

class GameController extends Controller
{
    public function show()
    {
        $room = auth()->user()->getCurrentGame();

        return view('user.game', compact('room'));
    }
}
