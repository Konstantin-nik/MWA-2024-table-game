<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\Round;
use App\Models\Participation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Action>
 */
class ActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'round_id' => Round::factory(),
            'participation_id' => Participation::factory(), 
            'chosen_deck' => $this->faker->numberBetween(0, 2), 
            'chosen_action' => $this->faker->numberBetween(0, 10), 
            'chosen_number' => $this->faker->numberBetween(1, 100), 
            'action_details' => $this->faker->optional()->json(), 
        ];
    }
}
