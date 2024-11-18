<x-main-layout>
    <div class="container mx-auto px-4 py-6">
        <!-- Room Title and Status -->
        <div class="flex items-center justify-between border-b pb-4 mb-6">
            <h1 class="text-3xl font-bold text-gray-800">{{ $room->name }} Room</h1>
            <div>
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
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-600">
                        <span class="font-medium">Room Size:</span> 
                        {{ $room->capacity }} players
                    </p>
                    <p class="text-gray-600">
                        <span class="font-medium">Current Players:</span> 
                        {{ count($room->users) }}
                    </p>
                    <p class="text-gray-600">
                        <span class="font-medium">Type:</span> 
                        {{ $room->is_public ? 'Public' : 'Private' }}
                    </p>
                </div>
                <div>
                    @if ($room->started_at)
                        <p class="text-gray-600">
                            <span class="font-medium">Started At:</span> 
                            {{ $room->started_at->format('M d, Y H:i') }}
                        </p>
                    @endif
                    @if ($room->finished_at)
                        <p class="text-gray-600">
                            <span class="font-medium">Finished At:</span> 
                            {{ $room->finished_at->format('M d, Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Players List -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Players</h2>
            @if (count($room->users) > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach ($room->users as $user)
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-gray-800 font-medium">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-400">{{ $user->pivot->joined_at->format('M d, Y H:i') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No players have joined this room yet.</p>
            @endif
        </div>

        <!-- Actions -->
        <div class="mt-6">
            @if (!$room->started_at && !$room->finished_at)
                <form method="POST" action="{{ route('rooms.show', $room->id) }}">
                    @csrf
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">
                        Join Room
                    </button>
                </form>
            @elseif ($room->started_at)
                <a href="{{ route('user.rooms.index') }}" class="text-blue-500 hover:underline">
                    Back to Rooms List
                </a>
            @endif
        </div>
    </div>
</x-main-layout>