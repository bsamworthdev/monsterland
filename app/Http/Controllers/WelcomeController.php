<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $monster_ids = ['6027','757','7027','7713','879','7290','5363'];
        $rand_key = array_rand($monster_ids);
        $monster_id = $monster_ids[$rand_key];

        return view('welcome', [
            "monster_id" => $monster_id,
        ]);
    }
}
