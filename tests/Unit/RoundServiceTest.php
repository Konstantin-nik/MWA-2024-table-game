<?php

use App\Events\RoundEnded;
use App\Models\Room;
use App\Models\Round;
use App\Services\RoundService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->roundService = new RoundService;
});

/** @test */
it('ends a round successfully and broadcasts an event', function () {
    Event::fake();
    Log::shouldReceive('info')->twice();

    $room = Room::factory()->create();
    $round = Round::factory()->for($room)->create();

    $this->roundService->endRound($round);

    expect($round->fresh()->finished_at)->not->toBeNull();
    Event::assertDispatched(RoundEnded::class);
});

/** @test */
it('starts a new round in a room', function () {
    Log::shouldReceive('info')->twice();

    $room = Room::factory()->create();
    Round::factory()->for($room)->create(['index' => 1]); // First round

    $this->roundService->startNewRound($room);

    expect($room->rounds()->count())->toBe(2);
    expect($room->rounds()->latest('index')->first()->index)->toBe(2);
});
