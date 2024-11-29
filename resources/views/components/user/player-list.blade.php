<dev class="divide-y divide-gray-200">
    @forelse ($participations as $participation)
        <x-user.player :participation="$participation" />
    @empty
        <p class="text-gray-500">No players have joined this room yet.</p>
    @endforelse
</dev>