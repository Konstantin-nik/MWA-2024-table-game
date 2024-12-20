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
  <header class="bg-white shadow">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-wrap justify-between items-center">
      <!-- Title and Text-Buttons -->
      <div class="flex items-center space-x-6">
        <!-- Title -->
        <a href="{{ route('user.rooms.index') }}" class="text-2xl font-bold text-gray-700 hover:text-blue-500 transition-colors">
          TableGame
        </a>
        <!-- Participation Button -->
        <a href="{{ route('user.participations') }}" class="text-xl font-medium text-gray-700 hover:text-blue-500 transition-colors">
          Participations
        </a>
        <!-- MyRooms Button -->
        <a href="{{ route('user.owned_rooms') }}" class="text-xl font-medium text-gray-700 hover:text-blue-500 transition-colors">
          My Rooms
        </a>
        <!-- CurrentGame Button -->
        <a href="{{ route('user.game') }}" class="text-xl font-medium text-gray-700 hover:text-blue-500 transition-colors">
          Current Game
        </a>
      </div>

      <!-- Navigation -->
      <nav class="flex flex-wrap items-center space-x-4 mt-2 md:mt-0">
          @include('components.form.join-by-token-form')
      </nav>
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

</body>
</html>
