<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    /**
     * Display the help page.
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->role === 'admin';

        return view('help.index', compact('user', 'isAdmin'));
    }
}
