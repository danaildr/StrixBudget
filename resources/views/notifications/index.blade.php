@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Notifications') }}</h1>
                    <button onclick="markAllAsRead()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
                        {{ __('Mark all as read') }}
                    </button>
                </div>
                
                <div id="notifications-container">
                    <!-- Notifications will be loaded here -->
                    <div class="text-center text-gray-500 py-8">
                        <svg class="animate-spin h-8 w-8 mx-auto text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2">{{ __('Loading notifications...') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load notifications
async function loadNotifications() {
    try {
        const response = await fetch('{{ route('notifications.index') }}');
        const notifications = await response.json();
        
        const container = document.getElementById('notifications-container');
        
        if (notifications.length === 0) {
            container.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <svg class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="mt-4 text-lg">{{ __('No notifications') }}</p>
                </div>
            `;
        } else {
            container.innerHTML = notifications.map(notification => `
                <div class="border-b border-gray-200 p-4 hover:bg-gray-50 ${!notification.read_at ? 'bg-blue-50' : ''}" data-notification-id="${notification.id}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">${notification.data.title}</h3>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">${formatDate(notification.created_at)}</span>
                                    ${!notification.read_at ? `
                                        <button onclick="markAsRead('${notification.id}')" class="text-blue-600 hover:text-blue-800 text-sm">
                                            {{ __('Mark as read') }}
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                            <p class="mt-1 text-gray-600">${notification.data.message}</p>
                            ${notification.data.url ? `
                                <div class="mt-2">
                                    <a href="${notification.data.url}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        {{ __('View details') }} →
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
        document.getElementById('notifications-container').innerHTML = `
            <div class="text-center text-red-500 py-8">
                <p>{{ __('Error loading notifications') }}</p>
            </div>
        `;
    }
}

// Mark notification as read
async function markAsRead(notificationId) {
    try {
        const response = await fetch(`{{ route('notifications.markAsRead', ':id') }}`.replace(':id', notificationId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        if (response.ok) {
            loadNotifications(); // Reload notifications
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

// Mark all notifications as read
async function markAllAsRead() {
    try {
        const response = await fetch('{{ route('notifications.markAllAsRead') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        if (response.ok) {
            loadNotifications(); // Reload notifications
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
    }
}

// Format date function
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) {
        return '{{ __('Just now') }}';
    } else if (minutes < 60) {
        return `${minutes} {{ __('minutes ago') }}`;
    } else if (hours < 24) {
        return `${hours} {{ __('hours ago') }}`;
    } else if (days < 7) {
        return `${days} {{ __('days ago') }}`;
    } else {
        return date.toLocaleDateString();
    }
}

// Load notifications when page loads
document.addEventListener('DOMContentLoaded', loadNotifications);
</script>
@endsection
