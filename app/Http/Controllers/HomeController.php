<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use App\InfoMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user_id = Auth::User()->id;
        $unfinished_monsters = Monster::with('segments')
            ->where('status', '<>', 'complete')
            ->where('status', '<>', 'cancelled')
            ->get();

            // $top_monsters = Monster::withCount([
            // 'ratings as average_rating' => function($query) {
            //     $query->select(DB::raw('coalesce(avg(rating),0)'));
            // }, 
            // 'ratings as ratings_count'])
            // ->where('status', 'complete')
            // ->having('average_rating', '>', 0)
            // ->having('ratings_count', '>', 2)
            // ->orderBy('average_rating','desc')
            // ->take(6)
            // ->get();


            // Model::where('types_id', $specialism_id)
            //     ->withCount(['requests as requests_1' => function ($query) {
            //         $query->where('type', 1);
            //     }, 'requests as requests_2' => function ($query) {
            //         $query->where('type', 2);
            //     }])
        
            $info_messages = InfoMessage::where('start_date', '<', DB::raw('now()'))
                ->where('end_date', '>' , DB::raw('now()'))
                ->get();

        return view('home', [
            "unfinished_monsters" => $unfinished_monsters,
            "user_id" => $user_id,
            "info_messages" => $info_messages
        ]);
    }
    public function create(Request $request)
    {
        $monster = new Monster;
        $monster->name = $request->name;
        $monster->auth = 1;
        $monster->status = 'awaiting head';
        $monster->save();

        return response()->json([
            'id' => $monster->id
        ]);
    }
}
