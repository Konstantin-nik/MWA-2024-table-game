<x-main-layout>
    <div class="max-w-2xl mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-5 text-center">Create a New Room</h1>
        <form action="{{ route('user.rooms.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf

            <!-- Room Name -->
            <x-form.text name="name" label="Room Name" placeholder="Enter room name"/>

            <!-- Capacity -->
            <x-form.number name="capacity" placeholder="Enter capacity"/>

            <!-- Public or Private -->
            <x-form.checkbox name="is_public" label="Visibility" placeholder="Make this room public"/>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <x-form.submit label="Create Room"/>
                <x-form.cancel redirectTo="{{ route('user.rooms.index') }}"/>
            </div>
        </form>
    </div>
</x-main-layout>
