<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationKey;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if this is the first user (no validation needed for first user)
        $isFirstUser = User::count() === 0;

        if ($isFirstUser) {
            // First user doesn't need a registration key
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $role = 'admin';
            $registrationKey = null;
        } else {
            // All other users need a valid registration key
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'registration_key' => ['required', 'string', 'exists:registration_keys,key'],
            ]);

            // Check if registration key is valid
            $registrationKey = RegistrationKey::where('key', $request->registration_key)->first();

            if (!$registrationKey || !$registrationKey->isValid()) {
                return back()->withErrors(['registration_key' => 'Invalid or already used registration key.']);
            }

            $role = $registrationKey->role;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        // Mark registration key as used (if exists)
        if ($registrationKey) {
            $registrationKey->markAsUsed($user);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
