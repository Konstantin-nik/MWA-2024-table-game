<dev class="divide-y divide-gray-200">
    @forelse ($users as $user)
        <x-user.player :user="$user" />
    @empty
        <p class="text-gray-500">No players have joined this room yet.</p>
    @endforelse
</dev>