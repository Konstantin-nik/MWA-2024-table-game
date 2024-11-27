<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    protected $fillable = [
        'room_id',
        'deck_index',
    ];

    // Model Relations ------------------------------------------------------
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    // Model static functions -------------------------------------------------
    public static function createDecksForRoom($room_id)
    {
        $allCards = Card::createCardsForDeck();

        $shuffledCards = collect($allCards)->shuffle();
        $cardChunks = $shuffledCards->chunk(27);

        foreach ($cardChunks as $index => $chunk) {
            $deck = Deck::create([
                'room_id' => $room_id,
                'deck_index' => $index,
            ]);

            foreach ($chunk as $cardData) {
                Card::create(array_merge($cardData, ['deck_id' => $deck->id]));
            }
        }
    }
}
