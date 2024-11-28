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
            ['index' => 0, 'houses' => 10, 'pool_indexes' => [2, 6, 7]],
            ['index' => 1, 'houses' => 11, 'pool_indexes' => [0, 3, 7]],
            ['index' => 2, 'houses' => 12, 'pool_indexes' => [1, 6, 10]],
        ];

        foreach ($rowsData as $rowData) {
            $row = $board->rows()->create([
                'board_id' => $board->id,
                'index' => $rowData['index'],
            ]);

            for ($i = 0; $i < $rowData['houses']; $i++) {
                $row->houses()->create([
                    'position' => $i,
                    'has_pool' => in_array($i, $rowData['pool_indexes']),
                ]);
            }
        }
    }
}
