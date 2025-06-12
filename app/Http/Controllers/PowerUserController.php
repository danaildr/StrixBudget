<?php

namespace App\Http\Controllers;

use App\Models\RegistrationKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PowerUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->canManageRegistrationKeys()) {
                abort(403, 'Access denied. Admin or Power User role required.');
            }
            return $next($request);
        });
    }

    /**
     * Display registration keys management page
     */
    public function registrationKeys()
    {
        $keys = RegistrationKey::with(['creator', 'usedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('power-user.registration-keys', compact('keys'));
    }

    /**
     * Generate new registration key
     */
    public function generateKey(Request $request)
    {
        $request->validate([
            'description' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:user,power_user'] // Power users cannot create admin keys
        ]);

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
}
