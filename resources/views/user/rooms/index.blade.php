<x-main-layout>
    @livewire('rooms-list', [
        'title' => 'Available Rooms',
        'showCreate' => true,
        'emptyStateMessage' => null,
    ])
</x-main-layout>