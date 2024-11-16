<x-main-layout>
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">Available Rooms</h1>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($rooms as $room)
            <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $room->name }} Room</h3>
                
                <div class="flex items-center justify-between text-gray-500 text-sm mb-4">
                    <span>
                        Players: 
                        <span class="font-bold text-gray-800">{{ count($room->users) }}</span>
                        /
                        <span class="font-bold text-gray-800">{{ $room->size }}</span>
                    </span>
                    <span class="bg-blue-100 text-blue-600 text-xs font-medium py-1 px-2 rounded">
                        {{ $room->is_public ? 'Public' : 'Private' }}
                    </span>
                </div>
                
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    @if ($room->started_at)
                        <span class="text-green-500 font-semibold">Game Started</span>
                    @else
                        <span class="text-yellow-500 font-semibold">Open to Join</span>
                    @endif
                    @if ($room->finished_at)
                        <span class="text-red-500 font-semibold">Game Finished</span>
                    @endif
                </div>
                <div class="text-center">
                    <a 
                        href="{{ route('rooms.join', $room->id) }}" 
                        class="inline-block w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium mt-3 py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Join
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-main-layout>