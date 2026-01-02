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

                        <!-- Email Settings (SMTP) -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Email Settings (SMTP)') }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ __('Configure SMTP server for email notifications') }}</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- SMTP Enabled -->
                                <div class="md:col-span-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="smtp_enabled" value="1" 
                                            {{ old('smtp_enabled', $settings['email']->where('key', 'smtp_enabled')->first()->value ?? false) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('Enable SMTP Email Notifications') }}</span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500">{{ __('When enabled, the system will send email notifications using the configured SMTP server') }}</p>
                                </div>

                                <!-- SMTP Host -->
                                <div>
                                    <label for="smtp_host" class="block text-sm font-medium text-gray-700">{{ __('SMTP Host') }}</label>
                                    <input type="text" name="smtp_host" id="smtp_host" 
                                        value="{{ old('smtp_host', $settings['email']->where('key', 'smtp_host')->first()->value ?? '') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="smtp.gmail.com">
                                    @error('smtp_host')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SMTP Port -->
                                <div>
                                    <label for="smtp_port" class="block text-sm font-medium text-gray-700">{{ __('SMTP Port') }}</label>
                                    <input type="number" name="smtp_port" id="smtp_port" 
                                        value="{{ old('smtp_port', $settings['email']->where('key', 'smtp_port')->first()->value ?? '587') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="587">
                                    @error('smtp_port')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SMTP Username -->
                                <div>
                                    <label for="smtp_username" class="block text-sm font-medium text-gray-700">{{ __('SMTP Username') }}</label>
                                    <input type="text" name="smtp_username" id="smtp_username" 
                                        value="{{ old('smtp_username', $settings['email']->where('key', 'smtp_username')->first()->value ?? '') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="your-email@gmail.com">
                                    @error('smtp_username')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SMTP Password -->
                                <div>
                                    <label for="smtp_password" class="block text-sm font-medium text-gray-700">{{ __('SMTP Password') }}</label>
                                    <input type="password" name="smtp_password" id="smtp_password" 
                                        value="{{ old('smtp_password', $settings['email']->where('key', 'smtp_password')->first()->value ?? '') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="••••••••">
                                    <p class="mt-1 text-xs text-gray-500">{{ __('Leave blank to keep current password') }}</p>
                                    @error('smtp_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SMTP Encryption -->
                                <div>
                                    <label for="smtp_encryption" class="block text-sm font-medium text-gray-700">{{ __('SMTP Encryption') }}</label>
                                    <select name="smtp_encryption" id="smtp_encryption" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">{{ __('None') }}</option>
                                        <option value="tls" {{ old('smtp_encryption', $settings['email']->where('key', 'smtp_encryption')->first()->value ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('smtp_encryption', $settings['email']->where('key', 'smtp_encryption')->first()->value ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                    @error('smtp_encryption')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SMTP From Address -->
                                <div>
                                    <label for="smtp_from_address" class="block text-sm font-medium text-gray-700">{{ __('From Email Address') }}</label>
                                    <input type="email" name="smtp_from_address" id="smtp_from_address" 
                                        value="{{ old('smtp_from_address', $settings['email']->where('key', 'smtp_from_address')->first()->value ?? '') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="noreply@yourdomain.com">
                                    @error('smtp_from_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SMTP From Name -->
                                <div>
                                    <label for="smtp_from_name" class="block text-sm font-medium text-gray-700">{{ __('From Name') }}</label>
                                    <input type="text" name="smtp_from_name" id="smtp_from_name" 
                                        value="{{ old('smtp_from_name', $settings['email']->where('key', 'smtp_from_name')->first()->value ?? 'StrixBudget') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="StrixBudget">
                                    @error('smtp_from_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Test Email Button -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <script>
                                function testSmtpConnection() {
                                    console.log('SMTP Test function called!');
                                    
                                    const resultDiv = document.getElementById('smtp-test-result');
                                    const testButton = document.getElementById('test-smtp');
                                    
                                    if (!resultDiv || !testButton) {
                                        console.error('Required elements not found!');
                                        return;
                                    }
                                    
                                    // Show loading state
                                    testButton.disabled = true;
                                    testButton.innerHTML = 'Testing...';
                                    resultDiv.classList.remove('hidden');
                                    resultDiv.innerHTML = '<div class="text-blue-600 text-sm">Testing SMTP connection...</div>';
                                    
                                    // Collect form data using getElementById for reliability
                                    const smtpData = {
                                        smtp_host: document.getElementById('smtp_host')?.value || '',
                                        smtp_port: document.getElementById('smtp_port')?.value || '',
                                        smtp_username: document.getElementById('smtp_username')?.value || '',
                                        smtp_password: document.getElementById('smtp_password')?.value || '',
                                        smtp_encryption: document.getElementById('smtp_encryption')?.value || '',
                                        smtp_from_address: document.getElementById('smtp_from_address')?.value || '',
                                        smtp_from_name: document.getElementById('smtp_from_name')?.value || ''
                                    };
                                    
                                    console.log('Collected SMTP Data:', smtpData);
                                    
                                    // Debug: check if elements exist
                                    console.log('Elements check:', {
                                        smtp_host: !!document.getElementById('smtp_host'),
                                        smtp_port: !!document.getElementById('smtp_port'),
                                        smtp_username: !!document.getElementById('smtp_username'),
                                        smtp_password: !!document.getElementById('smtp_password'),
                                        smtp_encryption: !!document.getElementById('smtp_encryption'),
                                        smtp_from_address: !!document.getElementById('smtp_from_address'),
                                        smtp_from_name: !!document.getElementById('smtp_from_name')
                                    });
                                    
                                    // Validate required fields
                                    if (!smtpData.smtp_host || !smtpData.smtp_port || !smtpData.smtp_username) {
                                        console.log('Validation failed:', {
                                            host: smtpData.smtp_host,
                                            port: smtpData.smtp_port, 
                                            username: smtpData.smtp_username
                                        });
                                        resultDiv.innerHTML = '<div class="text-red-600 text-sm">Please fill in SMTP Host, Port, and Username fields.</div>';
                                        testButton.disabled = false;
                                        testButton.innerHTML = 'Test SMTP Connection';
                                        return;
                                    }
                                    
                                    // Send request
                                    fetch('{{ route("admin.settings.test-smtp") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify(smtpData)
                                    })
                                    .then(response => {
                                        console.log('Response status:', response.status);
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Response data:', data);
                                        if (data.success) {
                                            resultDiv.innerHTML = '<div class="text-green-600 text-sm">✓ ' + (data.message || 'SMTP connection successful!') + '</div>';
                                        } else {
                                            resultDiv.innerHTML = '<div class="text-red-600 text-sm">✗ ' + (data.message || 'SMTP connection failed') + '</div>';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        resultDiv.innerHTML = '<div class="text-red-600 text-sm">✗ Error testing SMTP: ' + error.message + '</div>';
                                    })
                                    .finally(() => {
                                        testButton.disabled = false;
                                        testButton.innerHTML = 'Test SMTP Connection';
                                    });
                                }
                                </script>
                                
                                <button type="button" id="test-smtp" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700" onclick="testSmtpConnection()">
                                    {{ __('Test SMTP Connection') }}
                                </button>
                                <div id="smtp-test-result" class="mt-2 hidden"></div>
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
