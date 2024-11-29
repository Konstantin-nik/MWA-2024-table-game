<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Deck;
use App\Models\Fence;
use App\Models\House;
use App\Models\Room;
use App\Models\Round;
use App\Models\Row;
use DB;
use Illuminate\Http\Request;
use Validator;

class GameController extends Controller
{
    public function show()
    {
        $room = auth()->user()->getCurrentGame();

        if ($room) {
            $participation = $room->participations()->where('user_id', auth()->user()->id)->first();
            $board = Board::with(['rows.houses', 'rows.fences'])->where('participation_id', $participation->id)->first();
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
        $gameData = json_decode($request->input('game_data'), true);

        $validatedData = Validator::make($gameData, [
            'selectedPairIndex' => 'required|integer|min:0|max:2',
            'selectedHouses' => 'required|array|min:1|max:2',
            'selectedHouses.*' => 'integer|exists:houses,id',
            'estateIndex' => 'nullable|integer',
            'fenceId' => 'nullable|integer|exists:fences,id',
            'action' => 'required|integer',
            'number' => 'required|integer',
        ])->validate();

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

        $isValidTurn = true;
        if (! $isValidTurn) {
            abort(403, 'Invalid turn.');
        }

        DB::transaction(function () use ($currentRound, $participation, $validatedData) {
            $houseId = $validatedData['selectedHouses'][0];
            $house = House::findOrFail($houseId);
            $board = $house->row->board;

            if ($board->participation->user_id != auth()->user()->id) {
                abort(403);
            }

            if ($house->number) {
                abort(403, 'This house has already been numbered.');
            }

            $house->update(['number' => $validatedData['number']]);

            if ($validatedData['action'] == 1) { // Fence
                if ($validatedData['fenceId'] === null) {
                    abort(403, 'No fence selected');
                }
                $fence = Fence::findOrFail($validatedData['fenceId']);

                if ($fence->is_constructed) {
                    abort(403, 'This fence has already been constructed.');
                }
                $fence->update(['is_constructed' => true]);
            } elseif ($validatedData['action'] == 2) { // Estate
                if ($validatedData['estateIndex'] === null) {
                    abort(403, 'No Estate selected.');
                }
                $estates = $board->estates_values;
                if (! isset($estates[$validatedData['estateIndex']])) {
                    abort(403, 'No such Estate.');
                }

                if ($estates[$validatedData['estateIndex']]['index'] >= count($estates[$validatedData['estateIndex']]['values']) - 1) {
                    abort(403, 'This estate connot be increased more.');
                }

                $estates[$validatedData['estateIndex']]['index'] += 1;
                $board->estates_values = $estates;
                $board->save();
            } elseif ($validatedData['action'] == 3) { // Landscape
                $row = Row::findOrFail($house->row_id);
                $currentIndex = $row->current_landscape_index;

                if ($currentIndex < count($row->landscape_values) - 1) {
                    $row->update(['current_landscape_index' => $currentIndex + 1]);
                }
            } elseif ($validatedData['action'] == 4) { // Pool
                if (! $house->has_pool) {
                    abort(403, 'This house have no pool');
                }
                $house->update(['is_pool_constructed' => true]);
                $board->update(['number_of_pools' => $board->number_of_pools + 1]);
            } elseif ($validatedData['action'] == 5) { // Agency
                $board->update(['number_of_agencies' => $board->number_of_agencies + 1]);
            } elseif ($validatedData['action'] == 6) { // Bis
                if (count($validatedData['selectedHouses']) != 2) {
                    abort(403, 'Wrong number of houses selected');
                }

                $houseBId = $validatedData['selectedHouses'][1];
                $houseB = House::findOrFail($houseBId);

                if ($houseB->row->board->participation->user_id != auth()->user()->id) {
                    abort(403);
                }

                if ($houseB->number) {
                    abort(403, 'This houseB has already been numbered.');
                }

                $houseBNeighbour = House::where('row_id', $houseB->row_id)
                    ->whereNotNull('number')
                    ->where(function ($query) use ($houseB) {
                        $query->where('position', $houseB->position - 1)
                            ->orWhere('position', $houseB->position + 1);
                    })
                    ->first();

                if (! $houseBNeighbour) {
                    abort(403, 'No valid neighboring house found for Bis action.');
                }
                $houseB->update(['number' => $houseBNeighbour->number]);
                $board->update(['number_of_bises' => $board->number_of_bises + 1]);
            } else {
                abort(403, 'Invalid action');
            }

            $currentRound->actions()->create([
                'round_id' => $currentRound->id,
                'participation_id' => $participation->id,
                'chosen_deck' => $validatedData['selectedPairIndex'],  // Probably here will be issue, check show method and pass deck index to view
                'chosen_action' => $validatedData['action'],
                'chosen_number' => $validatedData['number'],
                'action_details' => json_encode([
                    'houses' => $validatedData['selectedHouses'],
                    'fence' => $validatedData['fenceId'],
                ]),
            ]);
        });

        // Check if all participants have taken their actions for the round
        $totalParticipations = $room->participations()->count();
        $totalActions = $currentRound->actions()->count();

        // if ($totalActions >= $totalParticipations) {
        if (true) {
            $this->endRound($currentRound, $room);
        }

        return redirect()->route('user.game')->with('success', 'Turn done successfully.');
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
