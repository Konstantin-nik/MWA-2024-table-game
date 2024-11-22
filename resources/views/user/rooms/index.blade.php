<x-main-layout>
    <div class="container mx-auto px-4 py-6">
        <!-- Title and Button -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-extrabold text-gray-800">Available Rooms</h1>
            <a href="{{ route('user.rooms.create') }}" 
               class="inline-flex items-center bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-medium px-5 py-2 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Room
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($rooms as $room)
                <x-room.card :room="$room" />
            @endforeach
        </div>
    </div>
</x-main-layout>
