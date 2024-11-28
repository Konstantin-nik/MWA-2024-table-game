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

            // Prepare three pairs of cards
            $currentRoundIndex = $room->rounds->last()?->index ?? 0;

            $cardPairs = $decks->map(function ($deck) use ($currentRoundIndex) {
                $actionCard = $deck->cards[$currentRoundIndex] ?? null;
                $numberCard = $deck->cards[$currentRoundIndex + 1] ?? null;

                return [
                    'numberCard' => $numberCard ? $numberCard->number : 'N/A',
                    'actionCard' => $actionCard ? $actionCard->action : null,
                ];
            })->take(3); // Limit to three pairs of cards

            return view('user.game', [
                'room' => $room,
                'participation' => $participation,
                'board' => $board,
                'cardPairs' => $cardPairs,
            ]);
        } else {
            return view('user.game', compact('room'));
        }
    }
}
