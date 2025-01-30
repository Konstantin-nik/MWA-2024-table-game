<?php

namespace App\Http\Controllers\Api;

use App\Enums\ActionType;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Services\ActionService;
use App\Services\GameOrchestrator;
use App\Services\GameService;
use App\Services\RoundService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Validator;

class GameController extends Controller
{
    protected $gameService;
    protected $roundService;
    protected $actionService;
    protected $gameOrchestrator;

    public function __construct(
        GameService $gameService,
        RoundService $roundService,
        ActionService $actionService,
        GameOrchestrator $gameOrchestrator
    ) {
        $this->gameService = $gameService;
        $this->roundService = $roundService;
        $this->actionService = $actionService;
        $this->gameOrchestrator = $gameOrchestrator;
    }

    /**
     * Get the current game state.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $room = auth()->user()->getCurrentGame();

        if ($room) {
            $participation = $this->gameService->getParticipationForUser($room, auth()->user()->id);
            $board = $participation->board()->with(['rows.houses', 'rows.fences'])->first();

            $currentRound = $this->gameService->getCurrentRound($room);
            $cardPairs = $this->gameOrchestrator->getCardPairsOrCreate($room, $currentRound->index);

            $numberOfAgents = $participation->actions()->where('chosen_action', ActionType::AGENT->value)->count();
            $agentsRank = $this->gameService->calculateAgentsRank($room, $numberOfAgents);

            return response()->json([
                'room' => $room,
                'participation' => $participation,
                'board' => $board,
                'cardPairs' => $cardPairs,
                'numberOfAgents' => $numberOfAgents,
                'agentsRank' => $agentsRank,
            ]);
        }

        return response()->json(['message' => 'No active game found.'], 404);
    }

    /**
     * Handle a player action.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function action(Request $request): JsonResponse
    {
        $gameData = json_decode($request->input('game_data'), true);

        $validatedData = Validator::make($gameData, [
            'selectedPairIndex' => 'required|integer|min:0|max:2',
            'selectedHouses' => 'required|array|min:1|max:2',
            'selectedHouses.*' => 'integer|exists:houses,id',
            'agentNumber' => 'nullable|integer|min:-2|max:2',
            'estateIndex' => 'nullable|integer',
            'fenceId' => 'nullable|integer|exists:fences,id',
            'action' => 'required|integer',
            'number' => 'required|integer',
        ])->validate();

        $user = auth()->user();
        $room = $user->getCurrentGame();

        if (!$room) {
            return response()->json(['message' => 'You are not in an active game.'], 403);
        }

        $participation = $this->gameService->getParticipationForUser($room, $user->id);
        $currentRound = $this->gameService->getCurrentRound($room);

        $this->actionService->handleAction($validatedData, $user->id, $currentRound, $participation);

        $result = $this->gameOrchestrator->handleActionEnd($currentRound, $room);

        if ($result === 'game_ended') {
            return response()->json(['message' => 'Game ended.', 'redirect' => route('api.game.end', $room->id)], 200);
        }

        return response()->json(['message' => 'Action handled successfully.'], 200);
    }

    /**
     * Handle a player skipping their turn.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function skip(Request $request): JsonResponse
    {
        $user = auth()->user();
        $room = $user->getCurrentGame();

        if (!$room) {
            return response()->json(['message' => 'You are not in an active game.'], 403);
        }

        $participation = $this->gameService->getParticipationForUser($room, $user->id);
        $currentRound = $this->gameService->getCurrentRound($room);

        $this->actionService->handleSkip($currentRound, $participation);

        $result = $this->gameOrchestrator->handleActionEnd($currentRound, $room);

        if ($result === 'game_ended') {
            return response()->json(['message' => 'Game ended.', 'redirect' => route('api.game.end', $room->id)], 200);
        }

        return response()->json(['message' => 'Turn skipped successfully.'], 200);
    }

    /**
     * Get the game end state.
     *
     * @param string $room_id
     * @return JsonResponse
     */
    public function end(string $room_id): JsonResponse
    {
        $room = Room::findOrFail($room_id);
        $participations = $room->participations()->with('user')->get()->sortByDesc('score');

        return response()->json(['participations' => $participations], 200);
    }
}