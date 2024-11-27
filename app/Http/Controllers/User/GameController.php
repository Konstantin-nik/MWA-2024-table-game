<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Deck;

class GameController extends Controller
{
    public function show()
    {
        $room = auth()->user()->getCurrentGame();
        if ($room) {
            $participation = $room->participations()->where("user_id", auth()->user()->id)->first();

            $board = Board::with(['rows.houses'])->where('participation_id', $participation->id)->first();

            $decks = Deck::with('cards')->where('room_id', $room->id)->get();

            return view('user.game', [
                'room' => $room,
                'participation' => $participation,
                'board' => $board,
                'decks' => $decks,
            ]);
        } else {
            return view('user.game', compact('room'));
        }
    }
}
