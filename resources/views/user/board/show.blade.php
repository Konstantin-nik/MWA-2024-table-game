<x-main-layout>
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
            Board of {{ $board->participation->user->name }}
        </h1>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <!-- General Board Information -->
            <h2 class="text-2xl font-semibold mb-4">Board Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><strong>Number of Pools:</strong> {{ $board->number_of_pools }}</div>
                <div><strong>Number of Agencies:</strong> {{ $board->number_of_agencies }}</div>
                <div><strong>Number of Bises:</strong> {{ $board->number_of_bises }}</div>
                <div><strong>Number of Skips:</strong> {{ $board->number_of_skips }}</div>
            </div>

            <!-- Participation Details -->
            <h2 class="text-2xl font-semibold mt-6 mb-4">Participation Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><strong>User:</strong> {{ $board->participation->user->name }}</div>
                <div><strong>Score:</strong> {{ $board->participation->score }}</div>
                <div><strong>Rank:</strong> {{ $board->participation->rank }}</div>
            </div>

            <!-- Score Breakdown -->
            <h2 class="text-2xl font-semibold mt-6 mb-4">Score Breakdown</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><strong>Estate Score:</strong> {{ $board->participation->scores['estateScore'] }}</div>
                <div><strong>Landscape Score:</strong> {{ $board->participation->scores['landscapeScore'] }}</div>
                <div><strong>Agent Bonus:</strong> {{ $board->participation->scores['agentBonus'] }}</div>
            </div>

            <!-- Estates -->
            <h2 class="text-2xl font-semibold mt-6 mb-4">Estates</h2>
            <div class="grid gap-6 grid-cols-1 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($board->estates_values as $index => $estate)
                    <div class="p-4 bg-gray-100 rounded">
                        <h3 class="text-lg font-semibold mb-2">Estate {{ $index + 1 }}</h3>
                        <span class="px-3 py-1 bg-green-100 rounded">
                            {{ $estate['values'][$estate['index']] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Board Representation (Rows, Houses, Fences) -->
            <h2 class="text-2xl font-semibold mt-6 mb-4">Board</h2>
            <div class="space-y-6">
                @foreach ($board->rows as $rowIndex => $row)
                    <div class="mb-4 border-b-2 border-gray-300 pb-4 p-2">
                        <!-- Landscape Values -->
                        <div class="pb-1 landscape-values flex flex-row gap-2 mb-2 justify-end ml-auto">
                            @foreach ($row->landscape_values as $index => $value)
                                <div 
                                    class="px-2 py-1 bg-green-100 text-sm rounded border"
                                    :class="{
                                        'border-blue-500 ring ring-blue-300': {{ $row->current_landscape_index }} === {{ $index }}
                                    }"
                                >
                                    {{ $value }}
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Houses and Fences -->
                        <div class="flex items-end gap-2 mb-4 cursor-pointer ml-auto overflow-x-auto">
                            <div class="ml-auto"></div>
                            @foreach ($row->houses as $index => $house)
                                <!-- House -->
                                <div 
                                    x-ref="house_{{ $house->id }}" 
                                    class="flex flex-col items-center"
                                    :class="{
                                        'cursor-not-allowed opacity-50': !isSelectableHouse({{ $house }})
                                    }"
                                    @click="selectHouse({{ $house }})"
                                >
                                    @if ($house->has_pool)
                                        <div 
                                            class="mb-1 w-12 h-5 pb-2 bg-blue-500 rounded"
                                            :class="{
                                                'border-4 border-black': isSelectedHouse({{ $house->id }}) && selectedAction === '4',
                                                'border-4 border-black': {{ $house->is_pool_constructed ? 'true' : 'false' }},
                                            }"
                                        ></div>
                                    @endif

                                    <div 
                                        class="flex items-center justify-center w-16 h-16 rounded border border-gray-400 bg-gray-200 text-lg font-bold"
                                        :class="{
                                            'border-4 border-black': isSelectedHouse({{ $house->id }}),
                                        }"
                                    >
                                        <span>{{ $house->number }}</span>
                                    </div>
                                </div>
                                
                                @if ($index < count($row->houses) - 1)
                                    @php
                                        $fence = $row->fences->firstWhere('position', $index);
                                    @endphp
                                    <div 
                                        class="flex items-center w-2 h-16 cursor-pointer flex-shrink-0" 
                                        :class="{
                                            'border-4 border-black': isSelectedFence({{ $fence->id }}),
                                            'bg-gray-500': {{ $fence->is_constructed ? 'true' : 'false'}},
                                            'bg-gray-200': {{ $fence->is_constructed ? 'false' : 'true'}},
                                        }"
                                        @click="selectFence({{ $fence }})"
                                    ></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Created and Updated Timestamps -->
            <h2 class="text-2xl font-semibold mt-6 mb-4">Timestamps</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><strong>Created At:</strong> {{ $board->created_at }}</div>
                <div><strong>Updated At:</strong> {{ $board->updated_at }}</div>
            </div>
        </div>
    </div>
</x-main-layout>
