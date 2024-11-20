<?php

namespace Database\Seeders;

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

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
