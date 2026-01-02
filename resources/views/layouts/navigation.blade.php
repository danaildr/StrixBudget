<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        @php
                            $siteIconUrl = \App\Helpers\SettingsHelper::getSiteIconUrl();
                            $siteName = \App\Helpers\SettingsHelper::getSiteName();
                        @endphp
                        @if($siteIconUrl)
                            <img src="{{ $siteIconUrl }}" alt="{{ $siteName }}" class="h-9 w-9 object-contain rounded">
                        @else
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        @endif
                        <span class="ml-2 text-lg font-semibold text-gray-800">{{ $siteName }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:ms-10 sm:flex items-center">
                    <!-- Movements Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                                class="{{ request()->routeIs('transactions.*', 'transfers.*') ? 'inline-flex items-center align-middle px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out' : 'inline-flex items-center align-middle px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                            <span>{{ __('Movements') }}</span>
                            <svg class="fill-current h-4 w-4 ml-1 transition-transform duration-200" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100"
                             style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('transactions.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                    {{ __('Transactions') }}
                                </a>
                                <a href="{{ route('transfers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                    {{ __('Transfers') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <x-nav-link :href="route('master-data.index')" :active="request()->routeIs('master-data.*')">
                        {{ __('Master Data') }}
                    </x-nav-link>
                </div>
            </div>



            <!-- Help Button и Settings Dropdown в един контейнер -->
            <div class="hidden sm:flex sm:items-center space-x-4">
                <!-- Notification Bell -->
                <div class="relative" x-data="{ 
                    open: false, 
                    notifications: [], 
                    unreadCount: 0, 
                    loading: true,
                    async fetchNotifications() {
                        try {
                            console.log('Fetching notifications...');
                            const response = await fetch('{{ route('notifications.index') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });
                            console.log('Response status:', response.status);
                            console.log('Response headers:', response.headers);
                            
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            
                            const data = await response.json();
                            console.log('Received data:', data);
                            this.notifications = data;
                            this.unreadCount = this.notifications.filter(n => !n.read_at).length;
                            console.log('Unread count:', this.unreadCount);
                        } catch (error) {
                            console.error('Error fetching notifications:', error);
                        } finally {
                            this.loading = false;
                        }
                    },
                    async markNotificationAsRead(notificationId) {
                        try {
                            const response = await fetch(`{{ route('notifications.markAsRead', ':id') }}`.replace(':id', notificationId), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            
                            if (response.ok) {
                                // Update notification in the array
                                const notification = this.notifications.find(n => n.id === notificationId);
                                if (notification) {
                                    notification.read_at = new Date().toISOString();
                                }
                                this.unreadCount = this.notifications.filter(n => !n.read_at).length;
                            }
                        } catch (error) {
                            console.error('Error marking notification as read:', error);
                        }
                    },
                    async markAllNotificationsAsRead() {
                        try {
                            const response = await fetch('{{ route('notifications.markAllAsRead') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            
                            if (response.ok) {
                                // Mark all notifications as read
                                this.notifications.forEach(n => {
                                    n.read_at = new Date().toISOString();
                                });
                                this.unreadCount = 0;
                            }
                        } catch (error) {
                            console.error('Error marking all notifications as read:', error);
                        }
                    },
                    formatDate(dateString) {
                        const date = new Date(dateString);
                        const now = new Date();
                        const diff = now - date;
                        const minutes = Math.floor(diff / 60000);
                        const hours = Math.floor(diff / 3600000);
                        const days = Math.floor(diff / 86400000);
                        
                        if (minutes < 1) {
                            return 'Just now';
                        } else if (minutes < 60) {
                            return `${minutes} minutes ago`;
                        } else if (hours < 24) {
                            return `${hours} hours ago`;
                        } else if (days < 7) {
                            return `${days} days ago`;
                        } else {
                            return date.toLocaleDateString();
                        }
                    }
                }" 
                     x-init="fetchNotifications()">
                    <button @click="open = !open; if(open) fetchNotifications()" 
                            class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none rounded-lg hover:bg-gray-100"
                            title="Notifications">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="unreadCount > 0" 
                              class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
                              x-text="unreadCount">
                        </span>
                    </button>

                    <!-- Notification Dropdown Panel -->
                    <div x-show="open" 
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95">
                        <div class="py-1 bg-white">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                                    <button @click="markAllNotificationsAsRead" 
                                            class="text-sm text-blue-600 hover:text-blue-800" 
                                            :class="{ 'opacity-50 cursor-not-allowed': unreadCount === 0 }"
                                            :disabled="unreadCount === 0">
                                        Mark all as read
                                    </button>
                                </div>
                            </div>
                            
                            <div class="max-h-96 overflow-y-auto">
                                <template x-if="loading">
                                    <div class="px-4 py-8 text-center text-gray-500">
                                        <svg class="animate-spin h-8 w-8 mx-auto text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="mt-2">Loading notifications...</p>
                                    </div>
                                </template>
                                
                                <template x-if="!loading && notifications.length === 0">
                                    <div class="px-4 py-8 text-center text-gray-500">
                                        <p>No notifications</p>
                                    </div>
                                </template>
                                
                                <template x-for="notification in notifications" :key="notification.id">
                                    <a :href="notification.data.url || '#'" 
                                       @click.prevent="if(notification.data.url) { window.location.href = notification.data.url; markNotificationAsRead(notification.id); }"
                                       class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100"
                                       :class="{ 'bg-blue-50': !notification.read_at }">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 pt-0.5">
                                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-medium text-gray-900" x-text="notification.data.title"></p>
                                                <p class="text-sm text-gray-500" x-text="notification.data.message"></p>
                                                <p class="text-xs text-gray-400 mt-1" x-text="formatDate(notification.created_at)"></p>
                                            </div>
                                            <button @click.stop="markNotificationAsRead(notification.id)" 
                                                    class="ml-2 p-1 text-gray-400 hover:text-gray-500"
                                                    title="Mark as read">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('help.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 inline-flex items-center" title="{{ __('Help & User Guide') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="ml-2">{{ __('Help') }}</span>
                </a>
                @if(Auth::check())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            @if(Auth::user()->isAdmin())
                                <x-dropdown-link :href="route('admin.index')">
                                    {{ __('Admin Panel') }}
                                </x-dropdown-link>
                            @elseif(Auth::user()->isPowerUser())
                                <x-dropdown-link :href="route('power-user.registration-keys')">
                                    {{ __('Power User Panel') }}
                                </x-dropdown-link>
                            @endif
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 inline-flex items-center" title="{{ __('Log in') }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                        <span class="ml-2">{{ __('Log in') }}</span>
                    </a>
                @endif
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::check())
                <!-- Mobile Movements Section -->
                <div class="px-4 py-2 space-y-1">
                    <div class="font-medium text-base text-gray-800 px-3 py-2">{{ __('Movements') }}:</div>
                    <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                        {{ __('Transactions') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('transfers.index')" :active="request()->routeIs('transfers.*')">
                        {{ __('Transfers') }}
                    </x-responsive-nav-link>
                </div>

                <x-responsive-nav-link :href="route('master-data.index')" :active="request()->routeIs('master-data.*')">
                    {{ __('Master Data') }}
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('help.index')" :active="request()->routeIs('help.*')">
                {{ __('Help') }}
            </x-responsive-nav-link>
            @unless(Auth::check())
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Log in') }}
                </x-responsive-nav-link>
            @endunless
        </div>

        <!-- Responsive Settings Options -->
        @if(Auth::check())
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if(Auth::user()->isAdmin())
                        <x-responsive-nav-link :href="route('admin.index')">
                            {{ __('Admin Panel') }}
                        </x-responsive-nav-link>
                    @elseif(Auth::user()->isPowerUser())
                        <x-responsive-nav-link :href="route('power-user.registration-keys')">
                            {{ __('Power User Panel') }}
                        </x-responsive-nav-link>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endif
    </div>
</nav>
