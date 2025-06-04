<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:en,bg'],
        ]);

        $request->user()->update([
            'locale' => $validated['locale']
        ]);

        session(['locale' => $validated['locale']]);
        App::setLocale($validated['locale']);

        return redirect()->back()->with('status', 'locale-updated');
    }
} 