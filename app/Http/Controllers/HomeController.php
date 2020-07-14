<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use Illuminate\Support\Facades\Auth;

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
        $user_id = Auth::User()->id;
        $monsters = Monster::with('segments')
            ->where('in_progress', 0)
            ->get();

        return view('home', [
            "monsters" => $monsters,
            "user_id" => $user_id
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
