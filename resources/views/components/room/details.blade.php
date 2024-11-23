<div class="bg-white shadow-md rounded-lg p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-room.detail label="Room Size:" :value="$room->capacity . ' players'" />
        <x-room.detail label="Current Players:" :value="$room->users()->count()" />
        <x-room.detail label="Type:" :value="$room->is_public ? 'Public' : 'Private'" />
    </div>
    <div>
        @if ($room->started_at)
            <x-room.detail label="Started At:" :value="$room->started_at->format('M d, Y H:i')" />
        @endif
        @if ($room->finished_at)
            <x-room.detail label="Finished At:" :value="$room->finished_at->format('M d, Y H:i')" />
        @endif
    </div>
</div>