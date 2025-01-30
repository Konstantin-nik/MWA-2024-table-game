<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Room;

class CardService
{
    /**
     * Get card pairs for the current round.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCardPairsByRoundIndex(Room $room, int $currentRoundIndex)
    {
        $cards = Card::with('deck')
            ->whereHas('deck', function ($query) use ($room) {
                $query->where('room_id', $room->id);
            })
            ->whereIn('position', [$currentRoundIndex, $currentRoundIndex + 1])
            ->get();

        $actionCards = $cards->where('position', $currentRoundIndex);
        $numberCards = $cards->where('position', $currentRoundIndex + 1);

        $maxPairs = min($actionCards->count(), $numberCards->count());
        if ($maxPairs == 0) {
            return collect();
        }

        return $actionCards->zip($numberCards)->map(function ($pair) {
            return [
                'numberCard' => $pair[1]->number ?? null,
                'actionCard' => $pair[0]->action ?? null,
            ];
        });
    }
}
