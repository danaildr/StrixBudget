<?php

namespace App\Http\Controllers;

class HelpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the help page.
     */
    public function index()
    {
        return view('help.index');
    }
}
