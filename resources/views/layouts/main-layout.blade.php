<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <script src="{{ mix('js/app.js') }}" defer></script>
    @endif
</head>
<body class="flex flex-col min-h-screen bg-gray-50 text-gray-800 font-sans">

  <!-- Header -->
  <header x-data="{ menuOpen: false }" class="bg-white shadow">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
      
      <!-- Logo -->
      <a href="{{ route('user.rooms.index') }}" class="text-2xl font-bold text-gray-700 hover:text-blue-500 transition-colors">
        TableGame
      </a>

      <!-- Mobile Menu Button -->
      <button @click="menuOpen = !menuOpen" class="md:hidden text-gray-700 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
      </button>


      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center space-x-6">
        <a href="{{ route('dashboard') }}" class="block text-lg text-gray-700 hover:text-blue-500">Dashboard</a>
        <a href="{{ route('user.participations') }}" class="text-lg font-medium text-gray-700 hover:text-blue-500 transition-colors">Participations</a>
        <a href="{{ route('user.owned_rooms') }}" class="text-lg font-medium text-gray-700 hover:text-blue-500 transition-colors">My Rooms</a>
        <a href="{{ route('user.game') }}" class="text-lg font-medium text-gray-700 hover:text-blue-500 transition-colors">Current Game</a>
      </nav>

      <!-- Join By Token Form (Hidden on Small Screens) -->
      <div class="hidden md:block">
        @include('components.form.join-by-token-form')
      </div>
    </div>

    <!-- Mobile Menu (Alpine.js Controlled) -->
    <div x-show="menuOpen" class="md:hidden bg-gray-100">
      <div class="container mx-auto px-4 py-2 space-y-2">
        <a href="{{ route('dashboard') }}" class="block text-lg text-gray-700 hover:text-blue-500">Dashboard</a>
        <a href="{{ route('user.participations') }}" class="block text-lg text-gray-700 hover:text-blue-500">Participations</a>
        <a href="{{ route('user.owned_rooms') }}" class="block text-lg text-gray-700 hover:text-blue-500">My Rooms</a>
        <a href="{{ route('user.game') }}" class="block text-lg text-gray-700 hover:text-blue-500">Current Game</a>
        @include('components.form.join-by-token-form')
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{ $slot }}
  </main>

  <!-- Footer -->
  <footer class="bg-gray-100 text-gray-600 py-6 border-t">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap justify-between items-center space-y-4 md:space-y-0">
      <p class="text-sm text-center md:text-left">© 2024 Table Game. All rights reserved.</p>
      <div class="flex space-x-4">
        <a href="#" class="hover:text-blue-500 transition-colors">Twitter</a>
        <a href="#" class="hover:text-blue-500 transition-colors">GitHub</a>
        <a href="#" class="hover:text-blue-500 transition-colors">LinkedIn</a>
      </div>
    </div>
  </footer>

@livewireScripts
</body>
</html>
