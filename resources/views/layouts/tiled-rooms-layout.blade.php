<div class="container mx-auto px-4 py-6">
    <!-- Title and Button -->
    <div class="flex items-center justify-between mb-6 ">
        <h1 class="text-2xl font-extrabold text-gray-800">{{ $title }}</h1>
        @if ($showCreate)
        <a href="{{ route('user.rooms.create') }}" 
            class="inline-flex items-center bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-medium px-5 py-2 rounded-lg shadow-lg transition-transform transform hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Room
        </a>
        @endif
    </div>

    @if ($rooms->isEmpty())
        <!-- No Rooms Message -->
        <div class="flex flex-col items-center justify-center text-center py-10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class=" size-20">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
            <p class="text-lg text-gray-600 font-medium">{{ $emptyStateMessage ? $emptyStateMessage : "No rooms available"}}</p>
            @if ($showCreate)
                <p class="text-gray-500 mt-2">Click the "Create Room" button above to get started.</p>
            @endif
        </div>
    @else
        <!-- Rooms Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($rooms as $room)
                <x-room.card :room="$room" />
            @endforeach
        </div>
    @endif
</div>