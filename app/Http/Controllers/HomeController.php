<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use App\User;
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
        $user = User::find($user_id);
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
            "info_messages" => $info_messages,
            "user_is_vip" => $user->vip
        ]);
    }
    public function create(Request $request)
    {
        $monster = new Monster;
        $monster->name = trim($request->name);

        $user_id = Auth::User()->id;
        $user = User::find($user_id);

        switch ($request->level){
            case 'basic':
                $monster->auth = 0;
                $monster->vip = 0;
                break;
            case 'standard':
                $monster->auth = 1;
                $monster->vip = 0;
                break;
            case 'pro':
                if (!$user->vip) return false;
                $monster->auth = 1;
                $monster->vip = 1;
                break;
        }

        $monster->status = 'awaiting head';
        $monster->save();

        header('Location: /canvas/'. $monster->id);

        // return response()->json([
        //     'id' => $monster->id
        // ]);
    }
}
