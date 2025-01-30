use App\Services\GameOrchestrator;
use PHPUnit\Framework\MockObject\MockObject;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    // Create a mock instance if needed
    $this->orchestrator = new GameOrchestrator();
});

test('it initializes the game correctly', function () {
    $game = $this->orchestrator->initializeGame();

    expect($game)->toBeObject()
        ->and($game->status)->toBe('initialized');
});

test('it processes player moves', function () {
    $game = $this->orchestrator->initializeGame();
    $result = $this->orchestrator->processMove($game, 'player1', 'move1');

    expect($result)->toBeTrue()
        ->and($game->moves)->toContain(['player1' => 'move1']);
});

test('it ends the game properly', function () {
    $game = $this->orchestrator->initializeGame();
    $this->orchestrator->endGame($game);

    expect($game->status)->toBe('ended');
});
