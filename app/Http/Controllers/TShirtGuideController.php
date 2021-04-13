<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TShirtGuideController extends Controller
{
    public function index()
    {
        return view('tshirtGuide');
    }
}
