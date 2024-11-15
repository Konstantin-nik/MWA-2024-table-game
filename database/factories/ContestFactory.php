<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contest>
 */
class ContestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->colorName,
            'size' => random_int(2, 10),
            'public' => fake()->boolean(80),
            'tocken' => fake()->word,
            'started_at' => null,
            'finished_at' => null,
        ];
    }

    /**
     * State for contests with a start and finish time.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withTimestamps(): Factory
    {
        return $this->state(function () {
            $startedAt = fake()->optional()->dateTime;

            return [
                'started_at' => $startedAt,
                'finished_at' => $startedAt ? fake()->dateTimeBetween($startedAt) : null,
            ];
        });
    }
}
