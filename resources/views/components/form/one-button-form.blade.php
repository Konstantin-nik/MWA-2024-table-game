<div class="mt-6">
    <form method="POST" action="{{ $redirectTo }}">
        @csrf
        <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">
            {{ $label }}
        </button>
    </form>
</div>