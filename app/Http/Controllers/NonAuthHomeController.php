<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use App\InfoMessage;
use App\Profanity;
use Illuminate\Support\Facades\DB;
use App\Session;
use Carbon\Carbon;

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

        //Group variables
        $group_id = $session->get('group_id') ? : 0;
        $group_name = $session->get('group_name') ? : '';
        $group_username = $session->get('group_username') ? : '';

        $unfinished_monsters = Monster::with('segments')
            ->where('status', '<>', 'complete')
            ->where('status', '<>', 'cancelled')
            ->where('nsfl', '0')
            ->where('nsfw', '0')
            ->where('group_id', $group_id)
            ->get(['id', 'name', 'in_progress', 'nsfw','nsfl','group_id','vip','status','auth',
                DB::Raw("(updated_at<'".Carbon::now()->subHours(1)->toDateTimeString()."') as abandoned") 
            ]);

        $info_messages = InfoMessage::where('start_date', '<', DB::raw('now()'))
            ->where('end_date', '>' , DB::raw('now()'))
            ->whereNull('user')
            ->get();

        return view('homeNonAuth', [
            "unfinished_monsters" => $unfinished_monsters,
            "session_id" => $session_id,
            "info_messages" => $info_messages,
            "group_mode" => $group_id > 0 ? 1 : 0,
            "group_name" => $group_name,
            "group_username" => $group_username
        ]);
    }
    public function create(Request $request)
    {
        $monster = new Monster;
        $name = $request->name;
        $session = $request->session();
        $group_id = $session->get('group_id') ? : 0;

        if ($name == "" || strlen($name) > 20){
            die();
        } else {
            $monster->name = $name;
        }

        $monster->auth = 0;
        $monster->status = 'awaiting head';
        $monster->group_id = $group_id;

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

        $monster->save();

        return response()->json([
            'id' => $monster->id
        ]);
    }
}
