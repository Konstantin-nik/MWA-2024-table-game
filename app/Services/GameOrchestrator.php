<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Round;

class GameOrchestrator
{
    public function __construct(
        protected RoundService $roundService,
        protected GameService $gameService,
        protected CardService $cardService,
        protected DeckService $deckService) {}

    /**
     * Get card pairs for the current round, or generate a new deck if no pairs are available.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCardPairsOrCreate(Room $room, int $currentRoundIndex)
    {
        $cardPairs = $this->cardService->getCardPairsByRoundIndex($room, $currentRoundIndex);

        if ($cardPairs->isEmpty()) {
            $this->deckService->generateNewDeck($room);

            return $this->cardService->getCardPairsByRoundIndex($room, $currentRoundIndex);
        }

        return $cardPairs;
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
