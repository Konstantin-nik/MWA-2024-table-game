<x-main-layout >
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">Participattions</h1>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($participations as $participation)
            <x-room.card :room="$participation->room"/>
        @endforeach
    </div>
</div>
</x-main-layout>