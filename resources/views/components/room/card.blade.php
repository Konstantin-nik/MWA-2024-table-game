<a href="{{ route('user.rooms.show', $room->id) }}" class="transform hover:scale-105 transition-transform duration-200">
    <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200 hover:border-blue-500 hover:shadow-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $room->name }} Room</h3>
        
        <div class="flex items-center justify-between text-gray-500 text-sm mb-4">
            <span>
                Players: 
                <span class="font-bold text-gray-800">{{ $room->users()->count() }}</span>
                /
                <span class="font-bold text-gray-800">{{ $room->capacity }}</span>
            </span>
            @if ($room->is_public)
                <span class="bg-green-100 text-green-600 text-xs font-medium py-1 px-2 rounded">
                    Public
                </span>
            @else 
                <span class="bg-yellow-100 text-yellow-600 text-xs font-medium py-1 px-2 rounded">
                    Private
                </span>
            @endif
        </div>
        
        <!-- Buttons -->
        @if (auth()->user()->canJoinRoom($room))
        <form method="POST" action="{{ route('user.rooms.join', $room) }}">
            @csrf
            <button 
                class="w-full bg-white text-gray-700 font-medium py-2 px-4 rounded border border-gray-300 hover:bg-gray-100 hover:shadow transition duration-200"
            >
                Join
            </button>
        </form>
        @elseif ($room->isFinished())
            <button 
                class="w-full bg-red-100 text-gray-700 font-medium py-2 px-4 rounded border border-gray-300 transition duration-200"
            >
                Game Finished
            </button>
        @elseif ($room->isStarted() && $room->isNotFinished())
            <button 
                class="w-full bg-yellow-100 text-gray-700 font-medium py-2 px-4 rounded border border-gray-300 transition duration-200"
            >
                Game Started
            </button>
        @elseif (auth()->user()->isInRoom($room))
            <button 
                class="w-full bg-green-100 text-gray-700 font-medium py-2 px-4 rounded border border-gray-300 transition duration-200"
            >
                You are in this room
            </button>
        @elseif ($room->isNotStartedOrFinished() && $room->isFull())
            <button 
                class="w-full bg-red-100 text-gray-700 font-medium py-2 px-4 rounded border border-gray-300 transition duration-200"
            >
                Full
            </button>
        @else
            <button 
                class="w-full bg-gray-100 text-gray-700 font-medium py-2 px-4 rounded border border-gray-300 transition duration-200"
            >
                Can't Join
            </button>
        @endif
    </div>
</a>
