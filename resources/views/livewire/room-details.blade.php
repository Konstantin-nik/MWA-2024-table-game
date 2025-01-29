<div>
    <div class="container max-w-3xl mx-auto px-4 py-6">
        <!-- Success Message -->
        @include("components.success-message")

        <!-- Room Title and Status -->
        <div class="flex items-center justify-between border-b pb-4 mb-6">
            <div class="flex items-end space-x-4">
                <h1 class="text-3xl font-bold text-gray-800">{{ $room->name }} Room</h1>

                <!-- Edit Button -->
                @if ($canEdit)
                    <a href="{{ route('user.rooms.edit', $room) }}" class="text-blue-500 hover:text-blue-600 font-small text-2xl">
                        Edit
                    </a>
                @endif

                <!-- Delete Button -->
                @if ($canDelete)
                    <form method="POST" action="{{ route('user.rooms.destroy', $room) }}" onsubmit="return confirm('Are you sure you want to delete this room?');">
                        @csrf
                        @method('delete')
                        <button type="submit" class="text-red-500 hover:text-red-600 font-small text-2xl">
                            Delete
                        </button>
                    </form>
                @endif

                <!-- Token Field -->
                <div 
                    id="tokenField" 
                    class="ml-4 flex items-center bg-gray-100 text-gray-800 px-4 py-1 rounded-md text-sm cursor-pointer shadow-sm hover:bg-gray-200"
                    onclick="copyToken()"
                >
                    <span class="mr-2">{{ $room->invitation_token }}</span>
                    <!-- Clipboard Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-4 -4 30 30" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <!-- Copied Message -->
                <x-copied-message :value="$room->invitation_token"/>
            </div>
            <div wire:poll.wire:poll.{{ $room->started_at || $room->finished_at ? '20s' : '3s' }}>
                @if ($room->finished_at)
                    <span class="text-sm bg-red-100 text-red-700 font-medium px-3 py-1 rounded">Game Finished</span>
                @elseif ($room->started_at)
                    <span class="text-sm bg-green-100 text-green-700 font-medium px-3 py-1 rounded">Game Started</span>
                @else
                    <span class="text-sm bg-yellow-100 text-yellow-700 font-medium px-3 py-1 rounded">Open to Join</span>
                @endif
            </div>
        </div>

        <!-- Room Details -->
        <x-panel>
            @livewire('room-details-section', ['roomId' => $room->id])
        </x-panel>

        <x-panel>
            <!-- Players List -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Players</h2>
            @livewire('players-list', ['roomId' => $room->id])

            <!-- Join/Leave button -->
            @if ($canJoin)
                <x-form.one-button-form label="Join" redirectTo="{{ route('user.rooms.join', $room) }}" />
            @elseif ($canLeave)
                <x-form.one-button-form label="Leave Room" redirectTo="{{ route('user.rooms.leave', $room) }}" />
            @endif
        </x-panel>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('user.rooms.index') }}" class="text-blue-500 hover:underline">
                Back to Rooms List
            </a>

            @if ($canStart)
                <form action="{{ route('user.rooms.start', $room) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary text-blue-500 hover:underline">Start Game</button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
            if (window.Echo) {
                // Listen for the GameStarted event
                window.Echo.join(`room.{{ $room->id }}`)
                    .listen('.game.started', (e) => {
                        // Redirect to the game route
                        window.location.href = "{{ route('user.game', $room->id) }}";
                    });
            } else {
                console.error('Echo is not initialized.');
            }
        });
</script>