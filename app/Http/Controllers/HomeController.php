<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use App\User;
use App\Profanity;
use App\InfoMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            ->where('nsfl', '0')
            ->when(!$user || $user->allow_nsfw == 0, function($q) {
                $q->where('nsfw', '0');
            })
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
        $name = trim($request->name);

        if ($name == "" || strlen($name) > 20){
            die();
        } else {
            $monster->name = $name;
        }

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
        $monster->nsfw = $request->nsfw ? 1 : 0;

        $profanity = Profanity::whereRaw('"'.$name.'" like CONCAT("%", word, "%")')
            ->orderBy('nsfl','desc')
            ->orderBy('nsfw','desc')
            ->get();

        if (count($profanity) > 0) {
            if ($profanity[0]->nsfw){
                $monster->nsfw = 1;
            }
            if ($profanity[0]->nsfl){
                $monster->nsfl = 1;
            }
        }

        $monster->status = 'awaiting head';
        $monster->save();

        header('Location: /canvas/'. $monster->id);
        die();

        // return response()->json([
        //     'id' => $monster->id
        // ]);
    }
    public function update(Request $request){

        $action = $request->action;

        if ($action == 'unblock'){
            $user_id = Auth::User()->id;
            if ($user_id != 1) die();

            $monsters = Monster::where('in_progress','1')
            ->where('updated_at', '<', 
                Carbon::now()->subHours(1)->toDateTimeString()
            )
            ->update(
                [
                'in_progress' => 0, 
                'in_progress_with' => 0, 
                'in_progress_with_session_id' => NULL
                ]
            );
        }
    }   
}
