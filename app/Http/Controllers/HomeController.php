<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $unfinished_monsters = Monster::all();

        return view('home', [
            "monsters" => $unfinished_monsters
        ]);
    }
    public function create(Request $request)
    {
        $monster = new Monster;
        $monster->name = $request->name;
        $monster->status = 'awaiting head';
        $monster->save();

        return response()->json([
            'id' => $monster->id
        ]);
    }
}
