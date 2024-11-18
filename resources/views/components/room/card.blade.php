<a href="{{ $auth ? route('user.rooms.show', $room->id) : route('rooms.show', $room->id) }}" class="transform hover:scale-105 transition-transform duration-200">
    <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200 hover:border-blue-500 hover:shadow-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $room->name }} Room</h3>
        
        <div class="flex items-center justify-between text-gray-500 text-sm mb-4">
            <span>
                Players: 
                <span class="font-bold text-gray-800">{{ count($room->users) }}</span>
                /
                <span class="font-bold text-gray-800">{{ $room->capacity }}</span>
            </span>
            <span class="bg-blue-100 text-blue-600 text-xs font-medium py-1 px-2 rounded">
                {{ $room->is_public ? 'Public' : 'Private' }}
            </span>
        </div>
        
        <div class="flex items-center space-x-2 text-sm text-gray-600">
            @if ($room->finished_at)
                <span class="text-red-500 font-semibold">Game Finished</span>
            @elseif ($room->started_at)
                <span class="text-green-500 font-semibold">Game Started</span>
            @else
                <span class="text-yellow-500 font-semibold">Open to Join</span>
            @endif
        </div>
    </div>
</a>