<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    protected $guarded = [];

    // Model Relations ------------------------------------------------------
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Model static functions -------------------------------------------------
    public static function createDecksForRoom($room_id, $stack = 0)
    {
        $allCards = Card::createCardsForDeck();

        $shuffledCards = collect($allCards)->shuffle();
        $cardChunks = $shuffledCards->chunk(27);

        foreach ($cardChunks as $index => $chunk) {
            $deck = Deck::create([
                'room_id' => $room_id,
                'index' => $index + $stack * 3,
            ]);

            foreach ($chunk->values() as $index2 => $cardData) {
                Card::create(array_merge($cardData, [
                    'deck_id' => $deck->id,
                    'position' => $index2 + $stack * 27,
                ]));
            }
        }
    }
}
