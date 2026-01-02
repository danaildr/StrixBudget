<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Models\RegistrationKey;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

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
            // SMTP validation
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl'],
            'smtp_from_address' => ['nullable', 'email', 'max:255'],
            'smtp_from_name' => ['nullable', 'string', 'max:255'],
            'smtp_enabled' => ['boolean'],
        ]);

        // Update general settings
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

        // Update SMTP settings
        SystemSetting::set('smtp_host', $request->smtp_host);
        SystemSetting::set('smtp_port', $request->smtp_port);
        SystemSetting::set('smtp_username', $request->smtp_username);
        SystemSetting::set('smtp_encryption', $request->smtp_encryption);
        SystemSetting::set('smtp_from_address', $request->smtp_from_address);
        SystemSetting::set('smtp_from_name', $request->smtp_from_name);
        SystemSetting::set('smtp_enabled', $request->boolean('smtp_enabled'));
        
        // Only update password if provided
        if ($request->filled('smtp_password')) {
            SystemSetting::set('smtp_password', $request->smtp_password);
        }

        // Clear settings cache
        SettingsHelper::clearCache();

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Test SMTP connection
     */
    public function testSmtp(Request $request)
    {
        try {
            // Validate required fields
            $data = $request->all();
            
            if (empty($data['smtp_host']) || empty($data['smtp_port']) || empty($data['smtp_username'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required SMTP configuration: host, port, or username'
                ]);
            }
            
            // Temporarily configure mail settings
            $config = [
                'transport' => 'smtp',
                'host' => $data['smtp_host'],
                'port' => $data['smtp_port'],
                'encryption' => $data['smtp_encryption'] ?? null,
                'username' => $data['smtp_username'],
                'password' => $data['smtp_password'] ?? '',
                'from' => [
                    'address' => $data['smtp_from_address'] ?? $data['smtp_username'],
                    'name' => $data['smtp_from_name'] ?? 'StrixBudget',
                ],
            ];

            try {
                // Create SMTP transport
                $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                    $config['host'],
                    $config['port']
                );
                $transport->setUsername($config['username']);
                $transport->setPassword($config['password']);

                $mailer = new \Symfony\Component\Mailer\Mailer($transport);
                
                // Create email
                $email = (new \Symfony\Component\Mime\Email())
                    ->from($config['from']['address'])
                    ->to(Auth::user()->email)
                    ->subject('SMTP Test - StrixBudget')
                    ->text('This is a test email to verify SMTP configuration is working correctly. If you receive this email, your SMTP settings are correct.');
                
                // Send the email
                $mailer->send($email);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Test email sent successfully to ' . Auth::user()->email
                ]);
                
            } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'SMTP connection failed: ' . $e->getMessage()
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
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
