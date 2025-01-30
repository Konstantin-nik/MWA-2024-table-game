<?php

namespace App\Http\Controllers\User;

use App\Enums\ActionType;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Services\ActionService;
use App\Services\GameOrchestrator;
use App\Services\GameService;
use App\Services\RoundService;
use Illuminate\Http\Request;
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
     * Displays the game view.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $room = auth()->user()->getCurrentGame();

        if ($room) {
            $participation = $this->gameService->getParticipationForUser($room, auth()->user()->id);
            $board = $participation->board()->with(['rows.houses', 'rows.fences'])->first();

            $currentRound = $this->gameService->getCurrentRound($room);
            $cardPairs = $this->gameOrchestrator->getCardPairsOrCreate($room, $currentRound->index);

            $numberOfAgents = $participation->actions()->where('chosen_action', ActionType::AGENT->value)->count();
            $agentsRank = $this->gameService->calculateAgentsRank($room, $numberOfAgents);

            return view('user.game', [
                'room' => $room,
                'participation' => $participation,
                'board' => $board,
                'cardPairs' => $cardPairs,
                'numberOfAgents' => $numberOfAgents,
                'agentsRank' => $agentsRank,
            ]);
        }

        return view('user.game', compact('room'));
    }

    /**
     * Handles a player action.
     *
     * @param  Request  $request  The incoming request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function action(Request $request)
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

        if (! $room) {
            abort(403, 'You are not in an active game.');
        }

        $participation = $this->gameService->getParticipationForUser($room, $user->id);
        $currentRound = $this->gameService->getCurrentRound($room);

        $this->actionService->handleAction($validatedData, $user->id, $currentRound, $participation);

        $result = $this->gameOrchestrator->handleActionEnd($currentRound, $room);

        if ($result === 'game_ended') {
            return redirect()->route('user.game.end', $room->id);
        }

        return redirect()->route('user.game');
    }

    /**
     * Handles a player skipping their turn.
     *
     * @param  Request  $request  The incoming request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function skip(Request $request)
    {
        $user = auth()->user();
        $room = $user->getCurrentGame();

        if (! $room) {
            abort(403, 'You are not in an active game.');
        }

        $participation = $this->gameService->getParticipationForUser($room, $user->id);
        $currentRound = $this->gameService->getCurrentRound($room);

        $this->actionService->handleSkip($currentRound, $participation);

        $result = $this->gameOrchestrator->handleActionEnd($currentRound, $room);

        if ($result === 'game_ended') {
            return redirect()->route('user.game.end', $room->id);
        }

        return redirect()->route('user.game');
    }

    /**
     * Displays the game end view.
     *
     * @param  string  $room_id  The ID of the room.
     * @return \Illuminate\View\View
     */
    public function end(string $room_id)
    {
        $room = Room::findOrFail($room_id);
        $participations = $room->participations()->with('user')->get()->sortByDesc('score');

        return view('user.game.end', compact('participations'));
    }
}
