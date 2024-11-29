<div class="py-3 flex items-center justify-between">
    <div>
        <p class="text-gray-800 font-medium">{{ $participation->user->name }}</p>
        <p class="text-sm text-gray-500">{{ $participation->user->email }}</p>
    </div>

    <!-- Display the score if participation exists -->
    @if ($participation->score)
        <p class="text-sm text-gray-600">Score: {{ $participation->score }}</p>
    @else
        <p class="text-sm text-gray-400">No score yet</p>
    @endif
</div>
