<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Round;

class GameOrchestrator
{
    protected $roundService;

    protected $gameService;

    public function __construct(RoundService $roundService, GameService $gameService)
    {
        $this->roundService = $roundService;
        $this->gameService = $gameService;
    }

    /**
     * Handles the end of a turn.
     *
     * @param  Round  $round  The current round.
     * @param  Room  $room  The room the round belongs to.
     * @return string The result of the turn (e.g., 'turn_ended', 'round_ended', 'game_ended').
     */
    public function handleActionEnd(Round $round, Room $room): string
    {
        if ($this->roundService->isRoundEnd($round)) {
            $this->roundService->endRound($round);

            if ($this->gameService->shouldEndGame($room)) {
                $this->gameService->endGame($room);

                return 'game_ended';
            }

            $this->roundService->startNewRound($room);

            return 'round_ended';
        }

        return 'action_ended';
    }
}
