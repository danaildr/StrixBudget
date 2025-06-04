<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Language Preferences') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Select your preferred language for the interface.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.locale.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="locale" :value="__('Language')" />
            <select
                id="locale"
                name="locale"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
            >
                <option value="en" {{ auth()->user()->locale === 'en' ? 'selected' : '' }}>English</option>
                <option value="bg" {{ auth()->user()->locale === 'bg' ? 'selected' : '' }}>Български</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('locale')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'locale-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section> 