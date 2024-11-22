<form action="{{ route('user.rooms.token.join') }}" method="POST" class="flex items-center space-x-2">
    @csrf
    <x-form.error-handling name="invitation_token"/>
    <input 
        type="text" 
        name="invitation_token"
        placeholder="Enter Room Token" 
        class="px-4 py-2 border border-gray-300 rounded-lg shadow-md text-gray-700 focus:ring-2 focus:ring-cyan-500 focus:outline-none"
    />
    <button type="submit" 
            class="inline-flex items-center bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-medium px-5 py-2 rounded-lg shadow-lg transition-transform transform hover:scale-105">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
        </svg>
        Join
    </button>
</form>