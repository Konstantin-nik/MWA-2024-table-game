<x-main-layout>
    @if ($room === null)
        <!-- No Game Message -->
        <div class="flex flex-col items-center justify-center text-center py-10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-20">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
            <p class="text-lg text-gray-600 font-medium">You are not in Game</p>
        </div>
    @else
        <div 
            x-data="gameLogic({{ json_encode($cardPairs) }})" 
            class="w-full max-w-6xl mx-auto p-6 bg-white shadow-md rounded-md"
        >
            <h1 class="text-2xl font-bold mb-4">Card & House Game</h1>

            <!-- Decks Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach ($cardPairs as $index => $pair)
                    <!-- Card Pair -->
                    <div 
                        class="flex flex-row items-center justify-center text-center space-x-5 p-4 bg-blue-100 rounded shadow cursor-pointer"
                        :class="{ 'ring ring-blue-500': selectedPairIndex === {{ $index }} }"
                        @click="selectPair({{ $index }})"
                    >
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
                <!-- Rows Section (Houses and Fences) -->
                @foreach ($board->rows as $rowIndex => $row)
                    <!-- Row Wrapper -->
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
                        <div class="flex items-end gap-2 mb-4 cursor-pointer justify-end ml-auto">
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
                                    <!-- Pool Above the House -->
                                    @if ($house->has_pool)
                                        <div 
                                            class="mb-1 w-12 h-5 pb-2 bg-blue-500 rounded"
                                            :class="{
                                                'border-4 border-black': isSelectedHouse({{ $house->id }}) && selectedAction === '4',
                                                'border-4 border-black': {{ $house->is_pool_constructed ? 'true' : 'false' }},
                                            }"
                                        ></div>
                                    @endif

                                    <!-- House Below -->
                                    <div 
                                        class="flex items-center justify-center w-16 h-16 rounded border border-gray-400 bg-gray-200 text-lg font-bold"
                                        :class="{
                                            'border-4 border-black': isSelectedHouse({{ $house->id }}),
                                        }"
                                    >
                                        <span>{{ $house->number }}</span>
                                    </div>
                                </div>
                                <!-- Fence -->
                                @if ($index < count($row->houses) - 1)
                                    @php
                                        $fence = $row->fences->firstWhere('position', $index);
                                    @endphp
                                    <div 
                                        class="fence w-2 h-16 cursor-pointer" 
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

                <!-- Buttons -->
                <form action="{{ route('user.game.action') }}" method="POST" id="end_turn_form">
                    @csrf
                    <textarea name="game_data" id="game_data" hidden></textarea>
                    <div class="flex justify-end mt-4 space-x-4">
                        <button 
                            id="end_turn_button"
                            type="button"
                            class="px-4 py-2 bg-green-500 text-white rounded shadow" 
                            @click="prepareEndTurn"
                            :disabled="!canEndTurn"
                        >
                            End Turn
                        </button>
                        <button 
                            type="button"
                            class="px-4 py-2 bg-red-500 text-white rounded shadow" 
                            @click="cancelTurn"
                        >
                            Cancel
                        </button>
                        <!-- Skip Turn Button -->
                        <button 
                            type="submit" 
                            formaction="{{ route('user.game.skip') }}" 
                            class="px-4 py-2 bg-gray-500 text-white rounded shadow"
                        >
                            Skip Turn
                        </button>
                    </div>
                </form>

                <!-- Current Board state -->
                <div>
                    <!-- Display Pool Values -->
                    <div class="mt-20 mb-6">
                        <h3 class="text-lg font-semibold">Pool Values</h3>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach ($board->pool_values as $index => $poolValue)
                                @if ($board->number_of_pools == $index)
                                    <div class="px-2 py-1 bg-blue-400 text-center rounded border">{{ $poolValue }}</div>
                                @else
                                    <div class="px-2 py-1 bg-blue-100 text-center rounded border">{{ $poolValue }}</div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Display Estates Values -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">Estates Values</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($board->estates_values as $estateIndex => $estate)
                                <div 
                                    class="p-4 bg-gray-100 rounded border"
                                    :class="{
                                        'border-4 border-black': isSelectedEstate({{ $estateIndex }}),
                                    }"
                                    @click="selectEstate({{ $estateIndex }})"
                                >
                                    <h4 class="font-semibold">Estate {{ $estateIndex + 1 }}</h4>
                                    <p class="text-sm">Index: {{ $estate['index'] }}</p>
                                    <p class="text-sm">Values: 
                                        @foreach ($estate['values'] as $value)
                                            {{ $value }}@if (!$loop->last), @endif
                                        @endforeach
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Display Bis Values -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">Bis Values</h3>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach ($board->bis_values as $index => $bisValue)
                                @if ($board->number_of_bises == $index)
                                    <div class="px-2 py-1 bg-red-400 text-center rounded border">{{ $bisValue }}</div>
                                @else
                                    <div class="px-2 py-1 bg-red-100 text-center rounded border">{{ $bisValue }}</div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Display Skiped Turns Penalty -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">Skip Turn Penalty</h3>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach ($board->skip_penalties as $index => $skipPenalty)
                                @if ($board->number_of_skips == $index)
                                    <div class="px-2 py-1 bg-red-400 text-center rounded border">{{ $skipPenalty }}</div>
                                @else
                                    <div class="px-2 py-1 bg-red-100 text-center rounded border">{{ $skipPenalty }}</div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Agency Number Selection Mini-Field -->
                <div 
                    x-show="selectAgency" 
                    x-transition
                    class="absolute flex justify-center items-center z-50"
                    :style="{ top: miniFieldPosition.top + 'px', left: miniFieldPosition.left + 'px' }"
                    @click.away="selectAgency = false"
                >
                    <div class="bg-white p-4 rounded shadow-lg">
                        <h3 class="text-lg font-bold mb-4">Select Number</h3>
                        <div class="flex gap-4">
                            <button type="button" @click="selectAgencyNumber(-2)" class="px-4 py-2 bg-blue-500 text-white rounded">
                                -2
                            </button>
                            <button type="button" @click="selectAgencyNumber(-1)" class="px-4 py-2 bg-blue-500 text-white rounded">
                                -1
                            </button>
                            <button type="button" @click="selectAgencyNumber(0)" class="px-4 py-2 bg-blue-500 text-white rounded">
                                0
                            </button>
                            <button type="button" @click="selectAgencyNumber(1)" class="px-4 py-2 bg-blue-500 text-white rounded">
                                +1
                            </button>
                            <button type="button" @click="selectAgencyNumber(2)" class="px-4 py-2 bg-blue-500 text-white rounded">
                                +2
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-gray-600">No board found for your participation.</p>
            @endif
        </div>
    @endif
</x-main-layout>

<script>
    function gameLogic(cardPairs) {
        return {
            selectedPairIndex: null,
            selectedAction: null,
            selectedNumber: null,
            selectedFenceId: null,
            selectedEstateNumber: null,
            selectAgency: false,
            selectedAgencyNumber: null,
            selectedHouses: [],
            miniFieldPosition: { top: 0, left: 0 },
            cardPairs,

            selectPair(index) {
                this.selectedPairIndex = index;
                const pair = this.cardPairs[index];
                this.selectedAction = pair.actionCard; 
                this.selectedNumber = pair.numberCard;
                this.selectedFenceId = null;
                this.selectedEstateNumber = null;
                this.selectAgency = false;
                this.selectedAgencyNumber = null;
                this.selectedHouses = [];

                console.log(`Selected Pair: ${index}, Action: ${this.selectedAction}, Number: ${this.selectedNumber}`);
            },

            isSelectableHouse(house) {
                if (!this.selectedAction) return false;
                if (house.number !== null) return false;
                if (this.selectedHouses.includes(house.id)) return true;

                switch (this.selectedAction) {
                    case "1": // Fence
                        if (this.selectedHouses.length > 0)
                            return false;

                        return true; 
                    case "2": // Estate
                        if (this.selectedHouses.length > 0)
                            return false;

                        return true;
                    case "3": // Landscape
                        if (this.selectedHouses.length > 0)
                            return false;

                        return true;
                    case "4": // Pool
                        if (this.selectedHouses.length > 0)
                            return false;

                        if (! house.has_pool)
                            return false; 

                        return true
                    case "5": // Agency
                        if (this.selectedHouses.length > 0)
                            return false;

                        return true;
                    case "6": // Bis
                        if (this.selectedHouses.length > 1)
                            return false;

                        return true; 
                    default:
                        return false;
                }
            },

            isSelectedHouse(houseId) {
                return this.selectedHouses.includes(houseId);
            },

            selectHouse(house) {
                if (this.selectedHouses.includes(house.id)) {
                    this.selectedHouses = this.selectedHouses.filter(id => id !== house.id);
                    console.log(`Unselected House: ${house.id}`);
                    return;
                }

                if (!this.isSelectableHouse(house)) {
                    console.log(`House ${house.id} is not selectable.`);
                    return;
                }

                if (this.selectedAction == 5) {
                    const houseElement = this.$refs[`house_${house.id}`];
                    const rect = houseElement.getBoundingClientRect();
                    console.log(rect);
                    this.miniFieldPosition = {
                        top: rect.top + window.scrollY + houseElement.offsetHeight + 10,
                        left: rect.left - rect.width / 2 + window.scrollX - 75
                    };

                    this.selectedHouses.push(house.id);
                    this.selectAgency = true;
                    console.log(`House ${house.id} selected for Agency. Please select a number.`);
                    return;
                }

                this.selectedHouses.push(house.id);
                console.log(`Selected Houses: ${this.selectedHouses}`);
            },

            isSelectableFence(fence) {
                if (fence.is_constructed) {
                    return false;
                }

                return true;
            },

            isSelectedFence(fenceId) {
                return this.selectedFenceId === fenceId;
            },

            selectFence(fence) {
                if (this.selectedAction !== "1") {
                    console.log("You must select the 'Fence' action first.");
                    return;
                }

                if (! this.isSelectableFence(fence)) {
                    console.log("This fence cannot be selected.");
                    return;
                }

                if (this.isSelectedFence(fence.id)) {
                    console.log(`Unselecting fence ${fence.id}.`);
                    this.selectedFenceId = null;
                    return;
                }

                console.log(`Placing fence ${fence.id}.`);

                this.selectedFenceId = fence.id;
            },

            get canEndTurn() {
                return this.selectedHouses.length > 0 && this.selectedAction !== null;
            },

            selectAgencyNumber(number) {
                if (this.selectedAction != 5) {
                    console.log("Wrong action");
                    return;
                }

                if (!this.selectedHouses.length) {
                    console.log("No house selected.");
                    return;
                }

                this.selectedAgencyNumber = Number(number);
                this.selectAgency = false;
                console.log(`Selected number: ${this.selectedNumber + this.selectedAgencyNumber}`);
            },

            isSelectableEstate(estate) {
                if (estate.index >= estate.values.length) {
                    return false;
                }

                return true;
            },

            isSelectedEstate(number) {
                return this.selectedEstateNumber === number;
            },

            selectEstate(number) {
                if (this.isSelectedEstate(number)) {
                    this.selectedEstateNumber = null;
                    console.log("Unselected estate");
                    return;
                }

                if (this.selectedAction != 2) {
                    console.log("Wrong action");
                    return;
                }

                this.selectedEstateNumber = number;
                console.log(`Selected estate: ${this.selectedEstateNumber}`);
            },

            prepareEndTurn() {
                const gameData = {
                    selectedPairIndex: this.selectedPairIndex,
                    selectedHouses: this.selectedHouses,
                    estateIndex: this.selectedEstateNumber,
                    agencyNumber: this.selectedAgencyNumber,
                    fenceId: this.selectedFenceId,
                    action: this.selectedAction,
                    number: this.selectedNumber,
                };
                document.getElementById('game_data').value = JSON.stringify(gameData);
                document.getElementById('end_turn_form').submit();
            },

            cancelTurn() {
                this.selectedPairIndex = null;
                this.selectedAction = null;
                this.selectedNumber = null;
                this.selectedFenceId = null;
                this.selectedEstateNumber = null;
                this.selectAgency = false;
                this.selectedAgencyNumber = null;
                this.selectedHouses = [];
                console.log('Turn cancelled.');
            },
        };
    }
</script>
