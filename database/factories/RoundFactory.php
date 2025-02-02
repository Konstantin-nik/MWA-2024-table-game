<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Round;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Round>
 */
class RoundFactory extends Factory
{
    protected $model = Round::class;

    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'index' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
