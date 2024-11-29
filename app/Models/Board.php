<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $guarded = [];

    protected $casts = [
        'pool_values' => 'array',
        'bis_values' => 'array',
        'estates_values' => 'array',
    ];

    // Model Relations --------------------------------------------------------
    public function rows()
    {
        return $this->hasMany(Row::class);
    }

    public function participation()
    {
        return $this->belongsTo(Participation::class);
    }

    // Model static functions -------------------------------------------------
    public static function initializeBoard(Participation $participation)
    {
        $board = Board::create([
            'participation_id' => $participation->id,
            'pool_values' => [0, 3, 6, 9, 13, 17, 21, 26, 31, 36],
            'bis_values' => [0, 1, 3, 6, 9, 12, 16, 20, 24, 28],
            'estates_values' => [
                'estate_one' => [
                    'values' => [1, 2],
                    'index' => 0
                ],
                'estate_two' => [
                    'values' => [2, 3, 4],
                    'index' => 0
                ],
                'estate_three' => [
                    'values' => [3, 4, 5, 6],
                    'index' => 0
                ],
                'estate_four' => [
                    'values' => [4, 5, 6, 7, 8],
                    'index' => 0
                ],
                'estate_five' => [
                    'values' => [5, 6, 7, 8, 10],
                    'index' => 0
                ],
                'estate_six' => [
                    'values' => [6, 7, 8, 10, 12],
                    'index' => 0
                ],
            ],
        ]);

        $rowsData = [
            ['index' => 0, 'houses' => 10, 'pool_indexes' => [2, 6, 7], 'landscape_values' => [0, 2, 4, 10]],
            ['index' => 1, 'houses' => 11, 'pool_indexes' => [0, 3, 7], 'landscape_values' => [0, 2, 4, 6, 14]],
            ['index' => 2, 'houses' => 12, 'pool_indexes' => [1, 6, 10], 'landscape_values' => [0, 2, 4, 6, 8, 18]],
        ];

        foreach ($rowsData as $rowData) {
            $row = $board->rows()->create([
                'board_id' => $board->id,
                'index' => $rowData['index'],
                'landscape_values' => $rowData['landscape_values'],
            ]);

            // Create Houses
            for ($i = 0; $i < $rowData['houses']; $i++) {
                $row->houses()->create([
                    'position' => $i,
                    'has_pool' => in_array($i, $rowData['pool_indexes']),
                ]);
            }

            // Create Fences between houses
            for ($i = 0; $i < $rowData['houses'] - 1; $i++) {
                $row->fences()->create([
                    'position' => $i,
                    'is_constructed' => false,
                ]);
            }
        }
    }
}
