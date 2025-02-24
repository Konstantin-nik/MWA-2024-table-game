<?php

namespace App\Services;

use App\Events\RoundEnded;
use App\Models\Room;
use App\Models\Round;
use Log;

class RoundService
{
    /**
     * Checks if the round has ended (all players have taken their actions).
     *
     * @param  Round  $round  The round to check.
     * @return bool True if the round has ended, false otherwise.
     */
    public function isRoundEnd(Round $round): bool
    {
        $totalParticipations = $round->room->participations()->count();
        $totalActions = $round->actions()->count();

        return $totalActions >= $totalParticipations;
    }

    /**
     * Ends the current round.
     *
     * @param  Round  $round  The round to end.
     * @return void
     */
    public function endRound(Round $round)
    {
        Log::info('Ending round', ['round_id' => $round->id]);

        try {
            $round->update(['finished_at' => now()]);
            Log::info('Round ended successfully', ['round_id' => $round->id]);
        } catch (\Exception $e) {
            Log::error('Failed to end round', [
                'round_id' => $round->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to end round.');
        }

        try {
            broadcast(new RoundEnded($round->room_id))->toOthers();
        } catch (\Exception $e) {
        }
    }

    /**
     * Starts a new round in the room.
     *
     * @param  Room  $room  The room to start the new round in.
     * @return void
     */
    public function startNewRound(Room $room)
    {
        Log::info('Starting new round', ['room_id' => $room->id]);

        try {
            $lastRound = $room->rounds()->latest('index')->first();
            $newRoundIndex = $lastRound ? $lastRound->index + 1 : 1;

            $room->rounds()->create(['index' => $newRoundIndex]);

            Log::info('New round started', ['room_id' => $room->id, 'round_index' => $newRoundIndex]);
        } catch (\Exception $e) {
            Log::error('Failed to start new round', [
                'room_id' => $room->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to start new round.');
        }
    }
}
