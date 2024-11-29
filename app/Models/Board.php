<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = ['participation_id'];

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
