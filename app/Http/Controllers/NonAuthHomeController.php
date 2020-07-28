<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use App\InfoMessage;
use Illuminate\Support\Facades\DB;
use App\Session;

class NonAuthHomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // $user_id = Auth::User()->id;
        //Users
        $session = $request->session();
        $session_id = $session->getId();

        $unfinished_monsters = Monster::with('segments')
            ->where('status', '<>', 'complete')
            ->where('status', '<>', 'cancelled')
            ->get();

        $info_messages = InfoMessage::where('start_date', '<', DB::raw('now()'))
            ->where('end_date', '>' , DB::raw('now()'))
            ->get();

        return view('homeNonAuth', [
            "unfinished_monsters" => $unfinished_monsters,
            "session_id" => $session_id,
            "info_messages" => $info_messages
        ]);
    }
    public function create(Request $request)
    {
        $monster = new Monster;
        $monster->name = $request->name;
        $monster->auth = 0;
        $monster->status = 'awaiting head';
        $monster->save();

        return response()->json([
            'id' => $monster->id
        ]);
    }
}
