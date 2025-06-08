<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Registration Key (only show if not first user) -->
        @php
            $isFirstUser = \App\Models\User::count() === 0;
        @endphp

        @if(!$isFirstUser)
            <div class="mt-4">
                <x-input-label for="registration_key" :value="__('Registration Key')" />
                <x-text-input id="registration_key" class="block mt-1 w-full" type="text" name="registration_key" :value="old('registration_key')" required />
                <x-input-error :messages="$errors->get('registration_key')" class="mt-2" />
                <p class="mt-1 text-sm text-gray-600">{{ __('You need a valid registration key to create an account. Contact an administrator to get one.') }}</p>
            </div>
        @else
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <p class="text-sm text-blue-800">
                    <strong>{{ __('First User Registration') }}</strong><br>
                    {{ __('You are registering as the first user and will automatically receive administrator privileges.') }}
                </p>
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
