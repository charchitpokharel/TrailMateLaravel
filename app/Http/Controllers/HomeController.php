<?php

namespace App\Http\Controllers;

use App\Models\Trail;

class HomeController extends Controller
{
    public function index()
    {
        $trails = Trail::latest()->take(6)->get();
        return view('home', compact('trails'));
    }
}
