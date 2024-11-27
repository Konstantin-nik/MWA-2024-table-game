<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'number',
        'action',
        'is_drawn',
        'deck_id',
    ];

    // Model Relations ------------------------------------------------------
    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    // Model static functions -------------------------------------------------
    public static function createCardsForDeck()
    {
        $cardsData = [
            1 => ['actions' => [1, 2, 3]],
            2 => ['actions' => [1, 2, 3]],
            3 => ['actions' => [1, 4, 5, 6]],
            4 => ['actions' => [2, 3, 4, 5, 6]],
            5 => ['actions' => [1, 1, 2, 2, 3, 3]],
            6 => ['actions' => [1, 1, 2, 3, 4, 5, 6]],
            7 => ['actions' => [1, 2, 2, 3, 3, 4, 5, 6]],
            8 => ['actions' => [1, 1, 2, 2, 3, 3, 4, 5, 6]],
            9 => ['actions' => [1, 2, 2, 3, 3, 4, 5, 6]],
            10 => ['actions' => [1, 1, 2, 3, 4, 5, 6]],
            11 => ['actions' => [1, 1, 2, 2, 3, 3]],
            12 => ['actions' => [2, 3, 4, 5, 6]],
            13 => ['actions' => [1, 4, 5, 6]],
            14 => ['actions' => [1, 2, 3]],
            15 => ['actions' => [1, 2, 3]],
        ];

        $cards = [];
        foreach ($cardsData as $number => $data) {
            foreach ($data['actions'] as $action) {
                $cards[] = [
                    'number' => $number,
                    'action' => $action,
                    'is_drawn' => false,
                ];
            }
        }

        return $cards;
    }
}
