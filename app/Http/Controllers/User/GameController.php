<?php

namespace App\Http\Controllers\User;

use App\Enums\ActionType;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Room;
use App\Services\ActionService;
use App\Services\GameOrchestrator;
use App\Services\GameService;
use App\Services\RoundService;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
            $cardPairs = $this->getCardPairsByRoundIndex($room, $currentRound->index);

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

    /**
     * Get card pairs and generate new deck if needed.
     *
     * @param  mixed  $currentRoundIndex
     * @return mixed
     */
    private function getCardPairsByRoundIndex(Room $room, int $currentRoundIndex)
    {
        $cards = Card::with('deck')
            ->whereHas('deck', function ($query) use ($room) {
                $query->where('room_id', $room->id);
            })
            ->whereIn('position', [$currentRoundIndex, $currentRoundIndex + 1])
            ->get();

        $actionCards = $cards->where('position', $currentRoundIndex);
        $numberCards = $cards->where('position', $currentRoundIndex + 1);

        $pairs = collect();
        $maxPairs = min($actionCards->count(), $numberCards->count());
        if ($maxPairs == 0) {
            $this->generateNewDeck($room);

            return $this->getCardPairsByRoundIndex($room, $currentRoundIndex);
        }

        for ($i = 0; $i < $maxPairs; $i++) {
            $actionCard = $actionCards->values()->get($i);
            $numberCard = $numberCards->values()->get($i);

            $pairs->push([
                'numberCard' => $numberCard ? $numberCard->number : null,
                'actionCard' => $actionCard ? $actionCard->action : null,
            ]);
        }

        return $pairs;
    }

    /**
     * Will generate new Decks for this room.
     *
     * @param  mixed  $currentRoundIndex
     * @return void
     */
    private function generateNewDeck(Room $room)
    {
        $lastDeckIndex = $room->decks->last()->index;
        $stack = ($lastDeckIndex + 1) / 3;
        Deck::createDecksForRoom($room->id, $stack);
    }
}
