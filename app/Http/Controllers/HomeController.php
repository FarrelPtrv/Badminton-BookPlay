<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $courts = Court::where('status', 'available')->get();
        return view('home', compact('courts'));
    }

    public function courts()
    {
        $courts = Court::all();
        return view('courts.index', compact('courts'));
    }

    public function courtDetail(Court $court)
    {
        return view('courts.show', compact('court'));
    }
}
