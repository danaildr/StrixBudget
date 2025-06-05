<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:en,bg'],
        ]);

        Log::debug('Changing locale to: ' . $validated['locale']);
        Log::debug('Current user locale: ' . $request->user()->locale);

        $request->user()->update([
            'locale' => $validated['locale']
        ]);

        Log::debug('Updated user locale in database');

        Session::put('locale', $validated['locale']);
        Log::debug('Updated locale in session');

        App::setLocale($validated['locale']);
        Log::debug('Set app locale');

        return redirect()->back()->with('status', 'locale-updated');
    }
} 