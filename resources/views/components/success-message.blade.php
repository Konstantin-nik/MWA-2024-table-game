@if(session('success'))
    <div class="bg-green-100 shadow-md border-green-500 text-green-700 p-4 mb-6 rounded-lg">
        <p class="font-semibold">{{ session('success') }}</p>
    </div>
@endif