<?php

namespace App\Services;

use App\Enums\ActionType;
use App\Models\Board;
use App\Models\Fence;
use App\Models\House;
use App\Models\Participation;
use App\Models\Round;
use App\Models\Row;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActionService
{
    /**
     * Handles a player action and creates an action record.
     *
     * @param array $validatedData The validated action data.
     * @param int $userId The ID of the user performing the action.
     * @param Round $round The current round.
     * @param Participation $participation The participation performing the action.
     * @return void
     */
    public function handleAction(array $validatedData, int $userId, Round $round, Participation $participation)
    {
        DB::transaction(function () use ($validatedData, $userId, $round, $participation) {
            $houseId = $validatedData['selectedHouses'][0];
            $house = House::with('row')->findOrFail($houseId);
            $board = $house->row->board;

            $this->validateOwnership($board, $userId);
            $this->validateHouseState($house);

            $houseNumber = $this->calculateHouseNumber($validatedData);

            $this->validateHouseNumberPlacement($house, $houseNumber);

            $house->update(['number' => $houseNumber]);

            $this->handleSpecificAction($validatedData, $house, $board);

            $this->createActionRecord($round, $participation, $validatedData, $houseNumber);
        });
    }

    /**
     * Validates that the user owns the board.
     *
     * @param Board $board The board to validate.
     * @param int $userId The ID of the user.
     * @return void
     */
    private function validateOwnership(Board $board, int $userId)
    {
        if ($board->participation->user_id != $userId) {
            abort(403, 'You do not own this board.');
        }
    }

    /**
     * Validates that the house is in a valid state for numbering.
     *
     * @param House $house The house to validate.
     * @return void
     */
    private function validateHouseState(House|Collection $house)
    {
        if ($house->number) {
            abort(403, 'This house has already been numbered.');
        }
    }

    /**
     * Calculates the house number based on the action.
     *
     * @param array $validatedData The validated action data.
     * @return int The calculated house number.
     */
    private function calculateHouseNumber(array $validatedData): int
    {
        return $validatedData['action'] == ActionType::AGENCY
            ? $validatedData['number'] + $validatedData['agencyNumber']
            : $validatedData['number'];
    }

    /**
     * Validates that the house number can be placed in the row.
     *
     * @param House $house The house to validate.
     * @param int $houseNumber The proposed house number.
     * @return void
     */
    private function validateHouseNumberPlacement(House|Collection $house, int $houseNumber)
    {
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
    }

    /**
     * Handles action-specific logic.
     *
     * @param array $validatedData The validated action data.
     * @param House $house The house being acted upon.
     * @param Board $board The board being updated.
     * @return void
     */
    private function handleSpecificAction(array $validatedData, House|Collection $house, Board $board)
    {
        switch ($validatedData['action']) {
            case ActionType::FENCE:
                $this->handleFenceAction($validatedData);
                break;
            case ActionType::ESTATE:
                $this->handleEstateAction($validatedData, $board);
                break;
            case ActionType::LANDSCAPE:
                $this->handleLandscapeAction($house->row);
                break;
            case ActionType::POOL:
                $this->handlePoolAction($house, $board);
                break;
            case ActionType::AGENCY:
                $this->handleAgencyAction($board);
                break;
            case ActionType::BIS:
                $this->handleBisAction($validatedData, $board);
                break;
            default:
                abort(403, 'Invalid action.');
        }
    }

    /**
     * Handles the fence action.
     *
     * @param array $validatedData The validated action data.
     * @return void
     */
    private function handleFenceAction(array $validatedData)
    {
        if ($validatedData['fenceId'] === null) {
            abort(403, 'No fence selected.');
        }
        $fence = Fence::findOrFail($validatedData['fenceId']);

        if ($fence->is_constructed) {
            abort(403, 'This fence has already been constructed.');
        }
        $fence->update(['is_constructed' => true]);
    }

    /**
     * Handles the estate action.
     *
     * @param array $validatedData The validated action data.
     * @param Board $board The board to update.
     * @return void
     */
    private function handleEstateAction(array $validatedData, Board $board)
    {
        if ($validatedData['estateIndex'] === null) {
            abort(403, 'No estate selected.');
        }
        $estates = $board->estates_values;
        if (!isset($estates[$validatedData['estateIndex']])) {
            abort(403, 'No such estate.');
        }

        if ($estates[$validatedData['estateIndex']]['index'] >= count($estates[$validatedData['estateIndex']]['values']) - 1) {
            abort(403, 'This estate cannot be increased further.');
        }

        $estates[$validatedData['estateIndex']]['index'] += 1;
        $board->estates_values = $estates;
        $board->save();
    }

    /**
     * Handles the landscape action.
     *
     * @param Row $row The row to update.
     * @return void
     */
    private function handleLandscapeAction(Row $row)
    {
        $currentIndex = $row->current_landscape_index;

        if ($currentIndex < count($row->landscape_values) - 1) {
            $row->update(['current_landscape_index' => $currentIndex + 1]);
        }
    }

    /**
     * Handles the pool action.
     *
     * @param House $house The house to update.
     * @param Board $board The board to update.
     * @return void
     */
    private function handlePoolAction(House $house, Board $board)
    {
        if (!$house->has_pool) {
            abort(403, 'This house has no pool.');
        }
        $house->update(['is_pool_constructed' => true]);
        $board->update(['number_of_pools' => $board->number_of_pools + 1]);
    }

    /**
     * Handles the agency action.
     *
     * @param Board $board The board to update.
     * @return void
     */
    private function handleAgencyAction(Board $board)
    {
        $board->update(['number_of_agencies' => $board->number_of_agencies + 1]);
    }

    /**
     * Handles the bis action.
     *
     * @param array $validatedData The validated action data.
     * @param Board $board The board to update.
     * @return void
     */
    private function handleBisAction(array $validatedData, Board $board)
    {
        if (count($validatedData['selectedHouses']) != 2) {
            abort(403, 'Wrong number of houses selected.');
        }

        $houseBId = $validatedData['selectedHouses'][1];
        $houseB = House::findOrFail($houseBId);

        if ($houseB->row->board->participation->user_id != auth()->user()->id) {
            abort(403, 'You do not own this house.');
        }

        if ($houseB->number) {
            abort(403, 'This house has already been numbered.');
        }

        $houseBNeighbour = House::where('row_id', $houseB->row_id)
            ->whereNotNull('number')
            ->where(function ($query) use ($houseB) {
                $query->where('position', $houseB->position - 1)
                    ->orWhere('position', $houseB->position + 1);
            })
            ->first();

        if (!$houseBNeighbour) {
            abort(403, 'No valid neighboring house found for Bis action.');
        }
        $houseB->update(['number' => $houseBNeighbour->number]);
        $board->update(['number_of_bises' => $board->number_of_bises + 1]);
    }

    /**
     * Creates an action record in the database.
     *
     * @param Round $round The current round.
     * @param Participation $participation The participation performing the action.
     * @param array $validatedData The validated action data.
     * @param int $houseNumber The house number assigned during the action.
     * @return void
     */
    private function createActionRecord(Round $round, Participation $participation, array $validatedData, int $houseNumber)
    {
        $round->actions()->create([
            'round_id' => $round->id,
            'participation_id' => $participation->id,
            'chosen_deck' => $validatedData['selectedPairIndex'],
            'chosen_action' => $validatedData['action'],
            'chosen_number' => $houseNumber,
            'action_details' => json_encode([
                'houses' => $validatedData['selectedHouses'],
                'fence' => $validatedData['fenceId'],
            ]),
        ]);
    }

    /**
     * Handles a player skipping their turn.
     *
     * @param Round $round The current round.
     * @param Participation $participation The participation skipping the turn.
     * @return void
     */
    public function handleSkip(Round $round, Participation $participation)
    {
        DB::transaction(function () use ($round, $participation) {
            // Create a skip action record
            $round->actions()->create([
                'round_id' => $round->id,
                'participation_id' => $participation->id,
                'chosen_deck' => -1,
                'chosen_action' => -1,
                'chosen_number' => -1,
                'action_details' => json_encode(['skip']),
            ]);

            // Increment the skip count on the board
            $board = $participation->board()->firstOrFail();
            if ($board->number_of_skips >= count($board->skip_penalties) - 1) {
                abort(403, 'You cannot skip any more turns. It should be the end of the game.');
            }

            $board->update(['number_of_skips' => $board->number_of_skips + 1]);
        });
    }
}