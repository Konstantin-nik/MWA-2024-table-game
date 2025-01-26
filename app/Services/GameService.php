<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Enums\ActionType;
use App\Events\GameEnded;
use App\Models\Board;
use App\Models\Participation;
use App\Models\Room;
use App\Models\Round;
use Log;

/**
 * Handles game-related logic, including scoring, ranking, and game lifecycle management.
 *
 * This service is responsible for calculating final scores, updating ranks, and managing
 * game-ending events. It interacts with models like Room, Participation, Board, and Round.
 */
class GameService
{
    /**
     * Retrieves the current round for a given room.
     *
     * @param  Room  $room  The room to retrieve the current round for.
     * @return Round The latest round for the room.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no round is found.
     */
    public function getCurrentRound(Room $room): Round
    {
        return $room->rounds()->latest('index')->firstOrFail();
    }

    /**
     * Retrieves the participation for a specific user in a given room.
     *
     * @param  Room  $room  The room to search for the participation.
     * @param  int  $userId  The ID of the user.
     * @return Participation The participation record for the user.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no participation is found.
     */
    public function getParticipationForUser(Room $room, int $userId): Participation
    {
        return $room->participations()->where('user_id', $userId)->firstOrFail();
    }

    /**
     * Checks if the game should end based on skip penalties.
     *
     * @param  Room  $room  The room to check.
     * @return bool True if the game should end, false otherwise.
     */
    public function shouldEndGame(Room $room): bool
    {
        $participations = $room->participations()->with('board')->get();

        foreach ($participations as $participation) {
            $board = $participation->board;

            if ($board && $board->number_of_skips >= count($board->skip_penalties) - 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ends the game for a given room by calculating final scores and updating the room's status.
     *
     * @param  Room  $room  The room to end the game for.
     * @return void
     */
    private function endGame(Room $room)
    {
        Log::info('Ending game for room', ['room_id' => $room->id]);

        try {
            $this->countFinalScores($room);
            $room->update(['finished_at' => now()]);
            broadcast(new GameEnded($room->id))->toOthers();

            Log::info('Game ended successfully for room', ['room_id' => $room->id]);
        } catch (\Exception $e) {
            Log::error('Failed to end game for room', [
                'room_id' => $room->id,
                'error' => $e->getMessage(),

            ]);
            throw new \RuntimeException('Failed to end game.');
        }
    }

    /**
     * Calculates final scores for all participations in a room and updates their ranks.
     *
     * @param  Room  $room  The room to calculate scores for.
     * @return void
     */
    private function countFinalScores(Room $room)
    {
        Log::info('Calculating final scores for room', ['room_id' => $room->id]);

        $participations = $room->participations()->with('board.rows')->get();

        $participations->map(function (Participation $participation) use ($room) {
            $scores = $this->calculateParticipationScores($participation, $room);
            $this->updateParticipationScores($participation, $scores);
        });

        $this->updateRanks($room);

        Log::info('Final scores calculated and ranks updated for room', ['room_id' => $room->id]);
    }

    /**
     * Calculates the scores for a specific participation.
     *
     * @param  Participation  $participation  The participation to calculate scores for.
     * @param  Room  $room  The room the participation belongs to.
     * @return array|null An associative array of scores, or null if no board is found.
     */
    private function calculateParticipationScores(Participation $participation, Room $room): ?array
    {
        $board = $participation->board;
        if (! $board) {
            Log::warning('No board found for participation', ['participation_id' => $participation->id]);

            return null;
        }

        Log::info('Calculating scores for participation', ['participation_id' => $participation->id]);

        $scores = [
            'poolScore' => $this->calculatePoolScore($board),
            'bisPenalty' => $this->calculateBisPenalty($board),
            'skipPenalty' => $this->calculateSkipPenalty($board),
            'estateScore' => $this->calculateEstateScore($board),
            'landscapeScore' => $this->calculateLandscapeScore($board),
            'agencyBonus' => $this->calculateAgencyBonus($room, $participation),
        ];

        Log::debug('Calculated scores for participation', [
            'participation_id' => $participation->id,
            'scores' => $scores,
        ]);

        return $scores;
    }

    /**
     * Calculates the pool score for a given board.
     *
     * @param  Board  $board  The board to calculate the score for.
     * @return int The calculated pool score.
     */
    private function calculatePoolScore(Board $board): int
    {
        $numberOfPools = $board->number_of_pools;

        return $board->pool_values[$numberOfPools] ?? 0;
    }

    /**
     * Calculates the Bis penalty for a given board.
     *
     * @param  Board  $board  The board to calculate the penalty for.
     * @return int The calculated Bis penalty.
     */
    private function calculateBisPenalty(Board $board): int
    {
        $numberOfBises = $board->number_of_bises;

        return $board->bis_values[$numberOfBises] ?? 0;
    }

    /**
     * Calculates the skip penalty for a given board.
     *
     * @param  Board  $board  The board to calculate the penalty for.
     * @return int The calculated skip penalty.
     */
    private function calculateSkipPenalty(Board $board): int
    {
        $numberOfSkips = $board->number_of_skips;

        return $board->skip_penalties[$numberOfSkips] ?? 0;
    }

    /**
     * Calculates the estate score for a given board.
     *
     * @param  Board  $board  The board to calculate the score for.
     * @return int The calculated estate score.
     */
    private function calculateEstateScore(Board $board): int
    {
        $estatesCount = $this->countEstates($board);
        $estateScore = 0;

        foreach ($estatesCount as $index => $estateCount) {
            $estate = $board->estates_values[$index];
            $estateScore += $estate['values'][$estate['index']] * $estateCount;
        }

        return $estateScore;
    }

    /**
     * Counts the number of estates of each size for a given board.
     *
     * @param  Board  $board  The board to count estates for.
     * @return array An array where the index represents the estate size minus one,
     *               and the value represents the count of estates of that size.
     */
    private function countEstates(Board $board): array
    {
        $estatesCount = [0, 0, 0, 0, 0, 0];

        foreach ($board->rows as $row) {
            $index = -1;
            $houses = $row->houses()->orderBy('position')->get();
            $fences = $row->fences()->orderBy('position')->get();

            while ($index < count($houses) - 1) {
                $estateSize = 0;
                $increment = 1;
                while ($index < count($houses) - 1) {
                    $index++;

                    if ($houses->get($index)->number !== null) {
                        $estateSize++;
                    } else {
                        $increment = 0;
                    }

                    if ($fences->get($index) !== null && $fences->get($index)->is_constructed) {
                        break;
                    }
                }
                if ($estateSize >= AppConstants::MIN_ESTATE_SIZE && $estateSize <= AppConstants::MAX_ESTATE_SIZE) {
                    $estatesCount[$estateSize - 1] += $increment;
                }
            }
        }

        return $estatesCount;
    }

    /**
     * Calculates the landscape score for a given board.
     *
     * @param  Board  $board  The board to calculate the score for.
     * @return int The calculated landscape score.
     */
    private function calculateLandscapeScore(Board $board): int
    {
        return $board->rows->sum(function ($row) {
            return $row->landscape_values[$row->current_landscape_index] ?? 0;
        });
    }

    /**
     * Calculates the agency bonus for a given participation.
     *
     * @param  Room  $room  The room the participation belongs to.
     * @param  Participation  $participation  The participation to calculate the bonus for.
     * @return int The calculated agency bonus.
     */
    private function calculateAgencyBonus(Room $room, Participation $participation)
    {
        $numberOfAgents = $participation->actions()->where('chosen_action', ActionType::AGENT)->count();
        $rank = $this->calculateAgentsRank($room, $numberOfAgents);

        return AppConstants::AGENT_REWARDS[$rank] ?? 0;
    }

    /**
     * Calculates the rank of a player based on the number of agents they have.
     *
     * @param  Room  $room  The room to calculate the rank for.
     * @param  int  $numberOfAgents  The number of agents the player has.
     * @return int The calculated rank.
     */
    public function calculateAgentsRank(Room $room, int $numberOfAgents)
    {
        $participations = $room->participations()->with('actions')->get();
        $agentsCounts = $participations->map(function ($participation) {
            return $participation->actions()->where('chosen_action', ActionType::AGENT)->count();
        })->sortDesc()->values();

        $rank = 1;
        foreach ($agentsCounts as $count) {
            if ($count > $numberOfAgents) {
                $rank++;
            } elseif ($count <= $numberOfAgents) {
                break;
            }
        }

        return $rank;
    }

    /**
     * Updates the scores for a given participation.
     *
     * @param  Participation  $participation  The participation to update.
     * @param  array  $scores  An associative array of scores.
     * @return void
     */
    private function updateParticipationScores(Participation $participation, array $scores)
    {
        $totalScore = $scores['poolScore'] + $scores['agencyBonus'] + $scores['landscapeScore']
            + $scores['estateScore'] - $scores['bisPenalty'] - $scores['skipPenalty'];

        if ($participation) {
            $participation->update([
                'scores' => $scores,
                'score' => $totalScore,
            ]);
        }

        Log::info('Updated scores for participation', [
            'participation_id' => $participation->id,
            'total_score' => $totalScore,
        ]);
    }

    /**
     * Updates the ranks for all participations in a room based on their scores.
     *
     * @param  Room  $room  The room to update ranks for.
     * @return void
     */
    private function updateRanks(Room $room)
    {
        Log::info('Updating ranks for room', ['room_id' => $room->id]);

        $participations = $room->participations()
            ->whereNotNull('score')
            ->orderByDesc('score')
            ->get();

        if ($participations->isEmpty()) {
            Log::warning('No participations with valid scores found for room', ['room_id' => $room->id]);

            return;
        }

        $rank = 1;
        $previousScore = $participations->first()->score;

        foreach ($participations as $participation) {
            $currentScore = $participation->score;

            if ($currentScore !== $previousScore) {
                $rank++;
            }

            $participation->rank = $rank;
            $participation->save();

            Log::debug('Updated rank for participation', [
                'participation_id' => $participation->id,
                'rank' => $rank,
                'score' => $currentScore,
            ]);

            $previousScore = $currentScore;
        }

        Log::info('Ranks updated for room', ['room_id' => $room->id]);
    }
}
