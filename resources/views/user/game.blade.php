<x-main-layout>
<div 
    x-data="game()"
    class="w-full max-w-4xl mx-auto p-6 bg-white shadow-md rounded-md"
>
    <h1 class="text-2xl font-bold mb-4">Card & House Game</h1>
    
    <!-- Decks Section -->
    <div class="flex gap-4 mb-6">
        <template x-for="(deck, deckIndex) in decks" :key="deckIndex">
            <div class="flex-1 p-4 bg-blue-100 rounded shadow">
                <h3 class="font-bold text-lg text-center">Deck <span x-text="deckIndex + 1"></span></h3>
                <button
                    x-on:click="selectCard(deckIndex)"
                    class="w-full mt-2 p-2 bg-blue-500 text-white font-semibold rounded disabled:bg-gray-300"
                    :disabled="deck.cards.length === 0"
                >
                    Draw Card
                </button>
                <p class="text-center mt-2">Top Card: <span x-text="deck.cards[0] ?? 'Empty'"></span></p>
            </div>
        </template>
    </div>

    <!-- Selected Card -->
    <div x-show="selectedCard" class="mb-6 text-center text-xl font-bold">
        Selected Card: <span class="text-blue-600" x-text="selectedCard"></span>
    </div>

    <!-- Houses Section -->
    <div>
        <template x-for="(row, rowIndex) in houses" :key="rowIndex">
            <div class="flex gap-2 mb-4">
                <template x-for="(house, houseIndex) in row" :key="houseIndex">
                    <div
                        x-on:click="placeCard(rowIndex, houseIndex)"
                        class="w-16 h-16 flex items-center justify-center bg-gray-200 rounded border border-gray-400 text-lg font-bold cursor-pointer hover:bg-gray-300"
                        :class="{'bg-blue-300': house !== null}"
                    >
                        <span x-text="house ?? 'Empty'"></span>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>

<script>
    function game() {
        return {
            // Game state
            decks: [
                { cards: [1, 2, 3, 4, 5] },
                { cards: [6, 7, 8, 9, 10] },
                { cards: [11, 12, 13, 14, 15] },
            ],
            selectedCard: null,
            houses: [
                [null, null, null, null, null],
                [null, null, null, null, null],
                [null, null, null, null, null],
            ],

            // Methods
            selectCard(deckIndex) {
                if (this.decks[deckIndex].cards.length === 0) return;
                this.selectedCard = this.decks[deckIndex].cards.shift(); // Draw the top card
            },
            placeCard(rowIndex, houseIndex) {
                if (!this.selectedCard) return; // No card selected
                if (this.houses[rowIndex][houseIndex] !== null) return; // House already occupied

                // Place the card
                this.houses[rowIndex][houseIndex] = this.selectedCard;
                this.selectedCard = null; // Clear the selected card
            },
        };
    }
</script>
</x-main-layout>
