<x-main-layout>
    @if ($room === null)
        <!-- No Game Message -->
        <div class="flex flex-col items-center justify-center text-center py-10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-20">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
            <p class="text-lg text-gray-600 font-medium">You are not in a Game</p>
        </div>
    @else
        <div class="w-full max-w-4xl mx-auto p-6 bg-white shadow-md rounded-md">
            <h1 class="text-2xl font-bold mb-4">Card & House Game</h1>

            <!-- Decks Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach ($cardPairs as $pair)
                    <!-- Card Pair -->
                    <div class="flex flex-col items-center space-y-2 p-4 bg-blue-100 rounded shadow text-center">
                        <!-- Card Above (Number Card) -->
                        <x-action-card type="number" :content="$pair['numberCard']" />
                        
                        <!-- Card Below (Action Card) -->
                        <x-action-card type="action" :content="$pair['actionCard']" />
                    </div>
                @endforeach
            </div>

            <!-- Boards Section -->
            <h2 class="text-xl font-semibold mb-4">Your Board</h2>
            @if ($board)
                @foreach ($board->rows as $row)
                    <div class="flex items-end gap-2 mb-4 justify-end ml-auto">
                        @foreach ($row->houses as $house)
                            <div class="flex flex-col items-center">
                                <!-- Pool Above the House -->
                                @if ($house->has_pool)
                                    <div class="mb-1 w-12 h-5 pb-2 bg-blue-500 rounded"></div>
                                @endif

                                <!-- House Below -->
                                <div class="flex items-center justify-center w-16 h-16 rounded border border-gray-400 bg-gray-200 text-lg font-bold">
                                    <span>{{ $house->number }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <p class="text-gray-600">No board found for your participation.</p>
            @endif
        </div>
    @endif
</x-main-layout>
