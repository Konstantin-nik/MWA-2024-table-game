<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Deck;
use App\Models\House;
use App\Models\Room;
use App\Models\Round;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function show()
    {
        $room = auth()->user()->getCurrentGame();

        if ($room) {
            $participation = $room->participations()->where('user_id', auth()->user()->id)->first();
            $board = Board::with(['rows.houses'])->where('participation_id', $participation->id)->first();
            $decks = Deck::with('cards')->where('room_id', $room->id)->get();

            // Prepare three pairs of cards
            $currentRoundIndex = $room->rounds->last()?->index ?? 0;

            $cardPairs = $decks->map(function ($deck) use ($currentRoundIndex) {
                $actionCard = $deck->cards[$currentRoundIndex] ?? null;
                $numberCard = $deck->cards[$currentRoundIndex + 1] ?? null;

                return [
                    'numberCard' => $numberCard ? $numberCard->number : null,
                    'actionCard' => $actionCard ? $actionCard->action : null,
                ];
            })->take(3);

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

    public function action(Request $request)
    {
        $request->validate([
            'selectedPairIndex' => 'required|integer|min:0|max:2',
            'selectedHouses' => 'required|array|min:1|max:2',
            'selectedHouses.*' => 'integer|exists:houses,id',
            'action' => 'required|integer',
            'number' => 'required|integer',
        ]);

        // dd($request);

        $user = auth()->user();
        $room = $user->getCurrentGame();

        if (! $room) {
            abort(403, 'You are not in an active game.');
        }

        $participation = $room->participations()->where('user_id', $user->id)->where('room_id', $room->id)->first();
        if (! $participation) {
            abort(403, 'You are not a participant in this game.');
        }

        $currentRound = $room->rounds()->latest('index')->first();
        if (! $currentRound || $currentRound->finished_at) {
            abort(403, 'No active round available.');
        }

        if ($currentRound->actions()->where('participation_id', $participation->id)->exists()) {
            abort(403, 'You have already taken your turn for this round.');
        }

        $isValidMove = true;
        if (! $isValidMove) {
            abort(400, 'Invalid move.');
        }

        $action = $currentRound->actions()->create([
            'round_id' => $currentRound->id,
            'participation_id' => $participation->id,
            'chosen_deck' => $request->selectedPairIndex,  // Probably here will be issue, check show method and pass deck index to view
            'chosen_action' => $request->action,
            'chosen_number' => $request->number,
            'action_details' => json_encode([
                'houses' => $request->selectedHouses,
            ]),
        ]);

        $house = House::findOrFail($request->selectedHouses[0]);
        $house->update(['number' => $request->number]);

        // Check if all participants have taken their actions for the round
        $totalParticipations = $room->participations()->count();
        $totalActions = $currentRound->actions()->count();

        // if ($totalActions >= $totalParticipations) {
        if (true) {
            $this->endRound($currentRound, $room);
        }

        return redirect()->route('user.game')->with('success', 'Move done successfully.');
    }

    private function endRound(Round $round, Room $room)
    {
        $round->update(['finished_at' => now()]);

        $newRoundIndex = $round->index + 1;
        $room->rounds()->create([
            'index' => $newRoundIndex,
        ]);
    }
}
