<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => $this->faker->numberBetween(1,10),
            'name' => fake()->colorName,
            'capacity' => random_int(2, 10),
            'is_public' => fake()->boolean(80),
            'invitation_token' => Str::random(10),
            'started_at' => null,
            'finished_at' => null,
        ];
    }

    /**
     * State for rooms with a start and finish time.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withTimestamps(): Factory
    {
        return $this->state(function () {
            $startedAt = fake()->optional()->dateTime;

            return [
                'started_at' => $startedAt,
                'finished_at' => $startedAt ? fake()->optional()->dateTimeBetween($startedAt) : null,
            ];
        });
    }
}
