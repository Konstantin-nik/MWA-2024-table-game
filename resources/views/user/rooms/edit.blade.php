<x-main-layout>
    <div class="max-w-2xl mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-5 text-center">Edit Room</h1>
        <form action="{{ route('user.rooms.update', $room) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('put')

            <!-- Room Name -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Room Name</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    placeholder="Enter room name" 
                    value="{{ old('name', $room->name) }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Capacity -->
            <div class="mb-4">
                <label for="capacity" class="block text-gray-700 text-sm font-bold mb-2">Capacity</label>
                <input 
                    type="number" 
                    name="capacity" 
                    id="capacity" 
                    placeholder="Enter capacity" 
                    value="{{ old('capacity', $room->capacity) }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('capacity') border-red-500 @enderror">
                @error('capacity')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Public or Private -->
            <div class="mb-4">
                <label for="is_public" class="block text-gray-700 text-sm font-bold mb-2">Visibility</label>
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_public" 
                        id="is_public" 
                        value="1" 
                        {{ old('is_public', $room->is_public) ? 'checked' : '' }} 
                        class="mr-2 leading-tight">
                    <label for="is_public" class="text-gray-600">Make this room public</label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Save Changes
                </button>
                <a href="{{ route('user.rooms.index') }}" 
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-main-layout>
