<?php

namespace Database\Seeders;

use App\Models\Participation;
use App\Models\Room;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        Room::factory(20)->withTimestamps()->create();

        foreach (Room::all() as $room) {
            $list_of_users = User::inRandomOrder()->take(random_int(0, $room->capacity))->get();

            $room->users()->attach($list_of_users);
        }

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $room = Room::create([
            'owner_id' => 3,
            'name' => 'Old Room',
            'capacity' => 3,
            'is_public' => true,
            'invitation_token' => 'Y3kbJsi3',
            'started_at' => now()->addDays(-2),
            'finished_at' => now()->addDays(-2),
        ]);

        Participation::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'score' => 80,
            'rank' => 1,
        ]);
    }
}
