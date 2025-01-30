<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Participation;

class BoardController extends Controller
{
    public function show(Participation $participation)
    {
        $board = $participation->board;

        return view('user.board.show', compact('board'));
    }
}
