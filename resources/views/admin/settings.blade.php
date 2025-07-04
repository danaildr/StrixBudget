<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- General Settings -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('General Settings') }}</h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Site Name -->
                                <div>
                                    <label for="site_name" class="block text-sm font-medium text-gray-700">{{ __('Site Name') }}</label>
                                    <input type="text" name="site_name" id="site_name" 
                                        value="{{ old('site_name', $settings['general']->where('key', 'site_name')->first()->value ?? 'StrixBudget') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        required>
                                    @error('site_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Appearance Settings -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Appearance Settings') }}</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Site Icon -->
                                <div>
                                    <label for="site_icon" class="block text-sm font-medium text-gray-700">{{ __('Site Icon') }}</label>
                                    <input type="file" name="site_icon" id="site_icon" accept="image/*"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">{{ __('Maximum file size: 2MB. Recommended size: 256x256px') }}</p>
                                    @error('site_icon')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    
                                    @php
                                        $currentIcon = $settings['appearance']->where('key', 'site_icon')->first()->value ?? null;
                                    @endphp
                                    @if($currentIcon)
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-600">{{ __('Current icon:') }}</p>
                                            <img src="{{ asset('storage/' . $currentIcon) }}" alt="Site Icon" class="mt-1 h-16 w-16 object-cover rounded">
                                        </div>
                                    @endif
                                </div>

                                <!-- Favicon -->
                                <div>
                                    <label for="favicon" class="block text-sm font-medium text-gray-700">{{ __('Favicon') }}</label>
                                    <input type="file" name="favicon" id="favicon" accept="image/*"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">{{ __('Maximum file size: 1MB. Recommended size: 32x32px or 16x16px') }}</p>
                                    @error('favicon')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    
                                    @php
                                        $currentFavicon = $settings['appearance']->where('key', 'favicon')->first()->value ?? null;
                                    @endphp
                                    @if($currentFavicon)
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-600">{{ __('Current favicon:') }}</p>
                                            <img src="{{ asset('storage/' . $currentFavicon) }}" alt="Favicon" class="mt-1 h-8 w-8 object-cover rounded">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                {{ __('Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
