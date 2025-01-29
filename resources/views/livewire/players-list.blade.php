<div wire:poll.{{ $room->started_at || $room->finished_at ? '' : '3s' }}="loadParticipations">
    @forelse ($participations as $participation)
        <x-user.player :participation="$participation" />
    @empty
        <p class="text-gray-500">No players have joined this room yet.</p>
    @endforelse
</div>
