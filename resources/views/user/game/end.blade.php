<x-main-layout>
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Game End Scores</h1>
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Player</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($participations as $index => $participation)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ $participation->user->name }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ $participation->score }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-main-layout>
