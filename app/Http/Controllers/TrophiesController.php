<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrophiesController extends Controller
{
    public function index()
    {
        return view('trophies');
    }
}
