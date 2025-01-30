<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Update Profile Information -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- API Token Management -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('API Tokens') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Generate API tokens to authenticate requests to your application.') }}
                            </p>
                        </header>

                        <!-- Display Success Message and Token -->
                        @if (session('status') && session('status.type') === 'success' && isset(session('status')['token']))
                            <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                                <p class="font-medium">Your new API token:</p>
                                <p class="break-all">{{ session('status')['token'] }}</p>
                                <p class="mt-2 text-sm">Make sure to copy this token now. You won’t be able to see it again!</p>
                            </div>
                        @endif

                        <!-- Display Existing Tokens -->
                        @if ($user->tokens->isNotEmpty())
                            <div class="mt-4">
                                <h3 class="text-md font-medium text-gray-900">
                                    {{ __('Existing Tokens') }}
                                </h3>

                                <ul class="mt-2">
                                    @foreach ($user->tokens as $token)
                                        <li class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">
                                                {{ $token->name }} (Last used: {{ $token->last_used_at ?? 'Never' }})
                                            </span>
                                            <form action="{{ route('profile.token.delete', $token->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Generate New Token Form -->
                        <form method="POST" action="{{ route('profile.token.create') }}" class="mt-6">
                            @csrf

                            <div>
                                <x-input-label for="token_name" :value="__('Token Name')" />
                                <x-text-input id="token_name" name="token_name" type="text" class="mt-1 block w-full" required autofocus />
                                <x-input-error :messages="$errors->get('token_name')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>
                                    {{ __('Generate Token') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Delete User -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>