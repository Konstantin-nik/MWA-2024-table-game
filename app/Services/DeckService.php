<?php

namespace App\Services;

use App\Models\Deck;
use App\Models\Room;

class DeckService
{
    public function generateNewDeck(Room $room): void
    {
        $lastDeckIndex = $room->decks->last()->index;
        $stack = ($lastDeckIndex + 1) / 3;
        Deck::createDecksForRoom($room->id, $stack);
    }
}
