<x-main-layout>
    <div class="container max-w-3xl mx-auto px-4 py-6">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 shadow-md border-green-500 text-green-700 p-4 mb-6 rounded-lg">
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Room Title and Status -->
        <div class="flex items-center justify-between border-b pb-4 mb-6">
            <div class="flex items-end space-x-4">
                <h1 class="text-3xl font-bold text-gray-800">{{ $room->name }} Room</h1>
                @if (auth()->user()->id == $room->owner_id)
                    <form method="GET" action="{{ route('user.rooms.edit', $room) }}">
                        @csrf
                        <button type="submit" class="text-blue-500 hover:text-blue-600 font-small text-2xl">
                            Edit
                        </button>
                    </form>
                @endif

                <!-- Copy Token Field -->
                <div 
                    id="tokenField" 
                    class="ml-4 flex items-center bg-gray-100 text-gray-800 px-4 py-1 rounded-md text-sm cursor-pointer shadow-sm hover:bg-gray-200"
                    onclick="copyToken()"
                >
                    <span class="mr-2">{{ $room->invitation_token }}</span>
                    <!-- Clipboard Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-4 -4 30 30" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <!-- Copied Message -->
                <div id="copiedMessage" 
                    class="fixed left-1/2 transform -translate-x-1/2 -translate-y-full bg-green-100 text-green-800 text-s font-medimum px-6 py-1 rounded shadow-md hidden">
                    Copied!
                </div>
            </div>
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
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No players have joined this room yet.</p>
            @endif
            @if (auth()->user()->canJoinRoom($room))
                <x-form.one-button-form label="Join" redirectTo="{{ route('user.rooms.join', $room) }}" />
            @elseif (auth()->user()->isInRoom($room))
                <x-form.one-button-form label="Leave Room" redirectTo="{{ route('user.rooms.leave', $room) }}" />
            @endif
        </div>

        <!-- Actions -->
        <div class="mt-6">
            <a href="{{ route('user.rooms.index') }}" class="text-blue-500 hover:underline">
                Back to Rooms List
            </a>
        </div>
    </div>
    <script>
        function copyToken() {
            const token = "{{ $room->invitation_token }}";
            const copiedMessage = document.getElementById('copiedMessage');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(token).then(() => {
                    copiedMessage.classList.remove('hidden');
                    setTimeout(() => {
                        copiedMessage.classList.add('hidden');
                    }, 1000);
                }).catch(err => {
                    console.error('Failed to copy token:', err);
                });
            } else {
                const textArea = document.createElement('textarea');
                textArea.value = token;
                textArea.style.position = 'fixed'; // Avoid scrolling to the bottom
                textArea.style.left = '-9999px'; // Move off-screen
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        copiedMessage.classList.remove('hidden');
                        setTimeout(() => {
                            copiedMessage.classList.add('hidden');
                        }, 1000);
                    }
                } catch (err) {
                    console.error('Fallback: Unable to copy token:', err);
                }
                document.body.removeChild(textArea);
            }
        }
    </script>

</x-main-layout>