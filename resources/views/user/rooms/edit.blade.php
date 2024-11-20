<x-main-layout>
    <div class="max-w-2xl mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-5 text-center">Edit Room</h1>
        <form action="{{ route('user.rooms.update', $room) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('put')

            <!-- Room Name -->
            <x-form.text name="name" label="Room Name" placeholder="Enter room name" value="{{ $room->name }}"/>

            <!-- Capacity -->
            <x-form.number name="capacity" placeholder="Enter capacity" value="{{ $room->capacity }}" />

            <!-- Public or Private -->
            <x-form.checkbox name="is_public" label="Visibility" placeholder="Make this room public" value="{{ $room->is_public }}"/>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <x-form.submit label="Save Changes"/>
                <x-form.cancel redirectTo="{{ route('user.rooms.show', $room) }}"/>
            </div>
        </form>
    </div>
</x-main-layout>
