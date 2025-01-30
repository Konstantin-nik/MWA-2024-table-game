<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
                <!-- Add the "Go to Game" button -->
                <div class="mt-6 mb-6 flex justify-center">
                    <a href="{{ route('user.rooms.index') }}" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md">
                        Go to Game
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
