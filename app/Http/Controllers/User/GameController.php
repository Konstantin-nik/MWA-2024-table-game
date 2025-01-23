<?php

namespace App\Http\Controllers\User;

use App\Events\RoundEnded;
use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Fence;
use App\Models\House;
use App\Models\Participation;
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

            // Prepare three pairs of cards
            $currentRoundIndex = $room->rounds->last()->index;

            $cardPairs = $this->getCardPairsByRoundIndex($room, $currentRoundIndex);

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
            'agencyNumber' => 'nullable|integer|min:-2|max:2',
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

        $cardPairs = $this->getCardPairsByRoundIndex($room, $currentRound->index);
        $noPairMatched = $cardPairs->every(function ($pair) use ($validatedData) {
            return ! ($validatedData['action'] == $pair['actionCard'] && $validatedData['number'] == $pair['numberCard']);
        });
        if ($noPairMatched) {
            abort(403, 'Invalid card.');
        }

        $isValidTurn = true;
        if (! $isValidTurn) {
            abort(403, 'Invalid turn.');
        }

        DB::transaction(function () use ($currentRound, $participation, $validatedData) {
            $houseId = $validatedData['selectedHouses'][0];
            $house = House::with('row')->findOrFail($houseId);
            $board = $house->row->board;

            if ($board->participation->user_id != auth()->user()->id) {
                abort(403);
            }

            if ($house->number) {
                abort(403, 'This house has already been numbered.');
            }

            if ($validatedData['action'] == 5) {
                $houseNumber = $validatedData['number'] + $validatedData['agencyNumber'];
            } else {
                $houseNumber = $validatedData['number'];
            }

            $leftHouse = $house->row->houses()
                ->where('position', '<', $house->position)
                ->whereNotNull('number')
                ->orderByDesc('position')
                ->first();

            $rightHouse = $house->row->houses()
                ->where('position', '>', $house->position)
                ->whereNotNull('number')
                ->orderBy('position')
                ->first();

            if (($leftHouse && $leftHouse->number >= $houseNumber) || ($rightHouse && $rightHouse->number <= $houseNumber)) {
                abort(403, 'House numbers must be in ascending order.');
            }

            $house->update(['number' => $houseNumber]);

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
                'chosen_number' => $houseNumber,
                'action_details' => json_encode([
                    'houses' => $validatedData['selectedHouses'],
                    'fence' => $validatedData['fenceId'],
                ]),
            ]);
        });

        // Check if all participants have taken their actions for the round
        $totalParticipations = $room->participations()->count();
        $totalActions = $currentRound->actions()->count();

        if ($totalActions >= $totalParticipations) {
            $this->endRound($currentRound, $room);
        }

        return redirect()->route('user.game')->with('success', 'Turn done successfully.');
    }

    public function skip(Request $request)
    {
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

        DB::transaction(function () use ($currentRound, $participation) {

            // Skip action
            $currentRound->actions()->create([
                'round_id' => $currentRound->id,
                'participation_id' => $participation->id,
                'chosen_deck' => -1,
                'chosen_action' => -1,
                'chosen_number' => -1,
                'action_details' => json_encode(['skip']),
            ]);

            $board = $participation->board()->firstOrFail();
            if ($board->number_of_skips >= count($board->skip_penalties) - 1) {
                abort(403, 'You cannot skip any more turns. It should be end of the game.');
            }

            $board->update(['number_of_skips' => $board->number_of_skips + 1]);
        });

        $board = $participation->board()->firstOrFail();
        if ($board->number_of_skips >= count($board->skip_penalties) - 1) {
            $this->countFinalScores($room);
            $room->update(['finished_at' => now()]);

            return redirect()->route('user.game.end', $room)->with('room_id', $room);
        }
        // Check if all participants have taken their actions for the round
        $totalParticipations = $room->participations()->count();
        $totalActions = $currentRound->actions()->count();

        if ($totalActions >= $totalParticipations) {
            // if (true) {
            $this->endRound($currentRound, $room);
        }

        return redirect()->route('user.game');
    }

    public function end(string $room_id)
    {
        $room = Room::findOrFail($room_id);

        $participations = $room->participations()->with('user')->get()->sortByDesc('score');

        return view('user.game.end', compact('participations'));
    }

    // Private functions

    private function endRound(Round $round, Room $room)
    {
        $round->update(['finished_at' => now()]);

        $newRoundIndex = $round->index + 1;
        $room->rounds()->create([
            'index' => $newRoundIndex,
        ]);

        broadcast(new RoundEnded($room))->toOthers();
    }

    private function countFinalScores(Room $room)
    {
        $participations = $room->participations()->with('board.rows')->get();

        $scores = $participations->map(function ($participation) {
            $board = $participation->board;
            if (! $board) {
                return null;
            }

            $number_of_pools = $board->number_of_pools;
            $number_of_bises = $board->number_of_bises;
            $number_of_skips = $board->number_of_skips;

            $pool_score = $board->pool_values[$number_of_pools] ?? 0;
            $bis_penalty = $board->bis_values[$number_of_bises] ?? 0;
            $skip_penalty = $board->skip_penalties[$number_of_skips] ?? 0;
            $agency_bonus = 7;

            $estates_count = [0, 0, 0, 0, 0, 0];
            foreach ($board->rows as $row) {
                $index = -1;
                $houses = $row->houses()->orderBy('position')->get();
                $fences = $row->fences()->orderBy('position')->get();

                while ($index < count($houses) - 1) {
                    $estate_size = 0;
                    $increment = 1;
                    while ($index < count($houses) - 1) {
                        $index++;

                        if ($houses->get($index)->number !== null) {
                            $estate_size++;
                        } else {
                            $increment = 0;
                        }

                        if ($fences->get($index) !== null && $fences->get($index)->is_constructed) {
                            break;
                        }
                    }
                    if ($estate_size > 0 && $estate_size < 7) {
                        $estates_count[$estate_size - 1] += $increment;
                    }
                }
            }

            $estate_score = 0;
            foreach ($estates_count as $index => $estate_count) {
                $estate = $board->estates_values[$index];
                $estate_score += $estate['values'][$estate['index']] * $estate_count;
            }

            $landscape_score = $board->rows->sum(function ($row) {
                return $row->landscape_values[$row->current_landscape_index] ?? 0;
            });

            return [
                'participation' => $participation->id,
                'score' => $pool_score + $agency_bonus + $landscape_score + $estate_score - $bis_penalty - $skip_penalty,
                'number_of_agencies' => $board->number_of_agencies,
            ];
        });

        $max_agencies = $scores->max('number_of_agencies');
        $scores = $scores->map(function ($score) use ($max_agencies) {
            if ($score['number_of_agencies'] !== $max_agencies) {
                $score['score'] -= 7;
            }

            return $score;
        });

        $scores = $scores->sortByDesc('score')->values();
        foreach ($scores as $index => $data) {
            $participation = Participation::find($data['participation']);
            if ($participation) {
                $participation->update([
                    'score' => $data['score'],
                    'rank' => $index,
                ]);
            }
        }
    }

    /**
     * Get card pairs and generate new deck if needed.
     *
     * @param  mixed  $currentRoundIndex
     * @return mixed
     */
    private function getCardPairsByRoundIndex(Room $room, $currentRoundIndex)
    {
        $cards = Card::with('deck')
            ->whereHas('deck', function ($query) use ($room) {
                $query->where('room_id', $room->id);
            })
            ->whereIn('position', [$currentRoundIndex, $currentRoundIndex + 1])
            ->get();

        $actionCards = $cards->where('position', $currentRoundIndex);
        $numberCards = $cards->where('position', $currentRoundIndex + 1);

        $pairs = collect();
        $maxPairs = min($actionCards->count(), $numberCards->count());
        if ($maxPairs == 0) {
            $this->generateNewDeck($room, $currentRoundIndex);

            return $this->getCardPairsByRoundIndex($room, $currentRoundIndex);
        }

        for ($i = 0; $i < $maxPairs; $i++) {
            $actionCard = $actionCards->values()->get($i);
            $numberCard = $numberCards->values()->get($i);

            $pairs->push([
                'numberCard' => $numberCard ? $numberCard->number : null,
                'actionCard' => $actionCard ? $actionCard->action : null,
            ]);
        }

        return $pairs;
    }

    /**
     * Will generate new Decks for this room.
     *
     * @param  mixed  $currentRoundIndex
     * @return void
     */
    private function generateNewDeck(Room $room, $currentRoundIndex)
    {
        $lastDeckIndex = $room->decks->last()->index;
        $stack = ($lastDeckIndex + 1) / 3;
        Deck::createDecksForRoom($room->id, $stack);
    }
}
