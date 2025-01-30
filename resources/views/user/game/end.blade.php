<x-main-layout>
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Game End Scores</h1>
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Player</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Score</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Details</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($participations as $index => $participation)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ $participation->user->name }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ $participation->score }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">
                                <div x-data="{ showDetails: false }">
                                    <button @click="showDetails = !showDetails" class="text-blue-500 hover:text-blue-700">
                                        <span x-text="showDetails ? 'Hide Details' : 'Show Details'"></span>
                                    </button>
                                    <div x-show="showDetails" class="mt-2">
                                        <ul>
                                            <li><strong>Pool Score:</strong> {{ $participation->scores['poolScore'] ?? 0 }}</li>
                                            <li><strong>Bis Penalty:</strong> {{ $participation->scores['bisPenalty'] ?? 0 }}</li>
                                            <li><strong>Skip Penalty:</strong> {{ $participation->scores['skipPenalty'] ?? 0 }}</li>
                                            <li><strong>Estate Score:</strong> {{ $participation->scores['estateScore'] ?? 0 }}</li>
                                            <li><strong>Landscape Score:</strong> {{ $participation->scores['landscapeScore'] ?? 0 }}</li>
                                            <li><strong>Agent Bonus:</strong> {{ $participation->scores['agentBonus'] ?? 0 }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">
                                <a href="{{ route('user.board.show', $participation->id) }}" class="text-blue-500 hover:text-blue-700">View Board</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-main-layout>