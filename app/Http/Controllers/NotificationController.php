<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Models\User;
use App\Notifications\GeneralNotification;

class NotificationController extends Controller
{
    /**
     * Показва всички непрочетени известия на потребителя
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications()->latest()->take(10)->get();
        
        // If request is AJAX (has X-Requested-With header or accepts JSON), return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($notifications);
        }
        
        // Otherwise return the view
        return view('notifications.index');
    }

    /**
     * Маркира известие като прочетено
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    /**
     * Маркира всички известия като прочетени
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Показва броя на непрочетените известия
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications->count();
        return response()->json(['count' => $count]);
    }
}
