<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Models\RegistrationKey;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                abort(403, 'Access denied. Admin role required.');
            }
            return $next($request);
        });
    }

    /**
     * Display the admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'power_users' => User::where('role', 'power_user')->count(),
            'total_keys' => RegistrationKey::count(),
            'used_keys' => RegistrationKey::where('is_used', true)->count(),
            'available_keys' => RegistrationKey::where('is_used', false)->count(),
        ];

        $serverStatus = $this->getServerStatus();

        return view('admin.index', compact('stats', 'serverStatus'));
    }

    /**
     * Display users management page
     */
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:admin,user,power_user']
        ]);

        // Prevent user from removing their own admin role
        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        // Ограничаваме админ ролята до един потребител
        if ($request->role === 'admin') {
            $existingAdmin = User::where('role', 'admin')->where('id', '!=', $user->id)->first();
            if ($existingAdmin) {
                return back()->with('error', 'There can only be one admin user. Current admin: ' . $existingAdmin->name);
            }
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "User role updated to {$request->role}.");
    }

    /**
     * Display registration keys management page
     */
    public function registrationKeys()
    {
        $keys = RegistrationKey::with(['creator', 'usedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.registration-keys', compact('keys'));
    }

    /**
     * Generate new registration key
     */
    public function generateKey(Request $request)
    {
        $request->validate([
            'description' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:admin,user,power_user']
        ]);

        // Ограничаваме създаването на админ ключове
        if ($request->role === 'admin') {
            $existingAdmin = User::where('role', 'admin')->first();
            if ($existingAdmin) {
                return back()->with('error', 'Cannot create admin registration key. There can only be one admin user.');
            }
        }

        $key = RegistrationKey::create([
            'key' => RegistrationKey::generateKey(),
            'description' => $request->description,
            'role' => $request->role,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', "Registration key generated: {$key->key}");
    }

    /**
     * Delete registration key
     */
    public function deleteKey(RegistrationKey $key)
    {
        if ($key->is_used) {
            return back()->with('error', 'Cannot delete used registration key.');
        }

        $key->delete();
        return back()->with('success', 'Registration key deleted.');
    }

    /**
     * Display system settings page
     */
    public function settings()
    {
        $settings = SystemSetting::getGrouped();
        return view('admin.settings', compact('settings'));
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_icon' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
        ]);

        // Update site name
        SystemSetting::set('site_name', $request->site_name);

        // Handle file uploads
        if ($request->hasFile('site_icon')) {
            $path = $request->file('site_icon')->store('settings', 'public');
            SystemSetting::set('site_icon', $path);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('settings', 'public');
            SystemSetting::set('favicon', $path);
        }

        // Clear settings cache
        SettingsHelper::clearCache();

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Get server status information
     */
    public function getServerStatus()
    {
        return [
            'php_version' => PHP_VERSION,
            'app_version' => '0.3-beta-20250721',
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
            'memory_limit' => ini_get('memory_limit'),
            'disk_free' => $this->formatBytes(disk_free_space('.')),
            'disk_total' => $this->formatBytes(disk_total_space('.')),
            'uptime' => $this->getSystemUptime(),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get system uptime (simplified version)
     */
    private function getSystemUptime()
    {
        if (function_exists('sys_getloadavg') && file_exists('/proc/uptime')) {
            $uptime = file_get_contents('/proc/uptime');
            $uptime = explode(' ', $uptime)[0];
            $days = floor($uptime / 86400);
            $hours = floor(($uptime % 86400) / 3600);
            $minutes = floor(($uptime % 3600) / 60);
            return "{$days}d {$hours}h {$minutes}m";
        }
        return 'N/A';
    }
}
