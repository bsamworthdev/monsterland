<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Monster;
use App\MonsterSegment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class HallOfFameSingleController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth','verified']);
    }

    //
    public function index(Request $request, $skip = 0, $time_filter = 'week', $search = ''){

        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = User::find($user_id);
            if (!isset($user->email_verified_at)){
                $user = NULL;
            }
            $group_id = 0;
        } else {
            $user = NULL;
            $session = $request->session();
            $group_id = $session->get('group_id') ? : 0;
        }

        switch ($time_filter){
            case 'day':
                $date = \Carbon\Carbon::today()->subHours(24);
            break;
            case 'week':
                $date = \Carbon\Carbon::today()->subDays(7);
            break;
            case 'month':
                $date = \Carbon\Carbon::today()->subWeeks(4);
            break;
            case 'year':
                $date = \Carbon\Carbon::today()->subWeeks(52);
            break;
            case 'ever':
                $date = \Carbon\Carbon::today()->subYears(10);
            break;
        }
        
        //if (!isset($monster_id)) {
            // $monster = Monster::without(['segments', 'ratings'])
            //     ->where('nsfl', '0')
            //     ->where('status','complete')
            //     ->when(!$user || $user->allow_nsfw == 0, function($q) {
            //         $q->where('nsfw', '0');
            //     })
            //     ->where('group_id',$group_id)
            //     ->orderBy('completed_at', 'desc')
            //     ->get(['id'])
            //     ->first();

            $monsters = Monster::withCount([
                'ratings as average_rating' => function($query) {
                    $query->select(DB::raw('coalesce(avg(rating),0)'));
                }, 
                'ratings as ratings_count'])
                ->where('status', 'complete')
                ->where('created_at','>=',$date)
                ->where('nsfl', '0')
                ->when(!$user || $user->allow_nsfw == 0, function($q) {
                    $q->where('nsfw', '0');
                })
                ->where('group_id', $group_id)
                ->where('name','LIKE','%'.$search.'%')
                ->having('average_rating', '>', 0)
                ->having('ratings_count', '>', 0)
                ->orderBy('average_rating','desc')
                ->orderBy('ratings_count', 'desc')
                ->orderBy('name', 'asc');
            
            $monsterCount = count($monsters->get());
                // $monster_id = $monster->id;

        //}

        // $monster = Monster::withCount([
        //     'ratings as average_rating' => function($query) {
        //         $query->select(DB::raw('coalesce(avg(rating),0)'));
        //     }])
        //     ->where('id', $monster_id)
        //     ->when(!$user || $user->id != 1, function($q) {
        //         $q->where('status','complete');
        //     })
        //     ->get()
        //     ->first();

        if ($monsterCount > 0){

            $monster = $monsters->skip($skip)->take(1)->get(['id','name'])->first();
            // $nextMonster = $monsters->skip($skip+1)->take(1)->get(['id','name'])->first();
            // $prevMonster = $monsters->skip($skip-1)->take(1)->get(['id','name'])->first();

            // $nextMonster = Monster::without(['segments', 'ratings'])
            //     ->where('id','<>', $monster_id)
            //     ->when($monster->completed_at, function($q) use($monster) {
            //         $q->where('completed_at','>', $monster->completed_at);
            //     })
            //     ->where('nsfl', '0')
            //     ->when(!$user || $user->allow_nsfw == 0, function($q) {
            //         $q->where('nsfw', '0');
            //     })
            //     ->where('status','complete')
            //     ->where('group_id', $group_id)
            //     ->orderBy('completed_at')
            //     ->get(['id','name'])
            //     ->first();
            
            // $nextMonster = $monster = Monster::withCount([
            //     'ratings as average_rating' => function($query) {
            //         $query->select(DB::raw('coalesce(avg(rating),0)'));
            //     }, 
            //     'ratings as ratings_count'])
            //     ->where('id','<>', $monster->id)
            //     ->where('status', 'complete')
            //     ->where('nsfl', '0')
            //     ->when(!$user || $user->allow_nsfw == 0, function($q) {
            //         $q->where('nsfw', '0');
            //     })
            //     ->where('group_id', $group_id)
            //     ->where('name','LIKE','%'.$search.'%')
            //     ->having('average_rating', '>', 0)
            //     ->having('ratings_count', '>', 0)
            //     ->orderBy('average_rating','desc')
            //     ->orderBy('ratings_count', 'desc')
            //     ->orderBy('name', 'asc')
            //     ->skip($skip+1)
            //     ->get(['id','name'])
            //     ->first();
                
            // // $prevMonster = Monster::without(['segments', 'ratings'])
            // //     ->where('id','<>', $monster_id)
            // //     ->when($monster->completed_at, function($q) use($monster) {
            // //         $q->where('completed_at','<', $monster->completed_at);
            // //     })
            // //     ->where('nsfl', '0')
            // //     ->when(!$user || $user->allow_nsfw == 0, function($q) {
            // //         $q->where('nsfw', '0');
            // //     })
            // //     ->where('status','complete')
            // //     ->where('group_id', $group_id)
            // //     ->orderBy('completed_at', 'desc')
            // //     ->get(['id','name'])
            // //     ->first();

            // $prevMonster = $monster = Monster::withCount([
            //     'ratings as average_rating' => function($query) {
            //         $query->select(DB::raw('coalesce(avg(rating),0)'));
            //     }, 
            //     'ratings as ratings_count'])
            //     ->where('id','<>', $monster->id)
            //     ->where('status', 'complete')
            //     ->where('nsfl', '0')
            //     ->when(!$user || $user->allow_nsfw == 0, function($q) {
            //         $q->where('nsfw', '0');
            //     })
            //     ->where('group_id', $group_id)
            //     ->where('name','LIKE','%'.$search.'%')
            //     ->having('average_rating', '>', 0)
            //     ->having('ratings_count', '>', 0)
            //     ->orderBy('average_rating','desc')
            //     ->orderBy('ratings_count', 'desc')
            //     ->orderBy('name', 'asc')
            //     ->skip($skip-1)
            //     ->get(['id','name'])
            //     ->first();
            
            return view('hallOfFameSingle', [
                'monster' => $monster,
                'user' => $user,
                'monsterCount' => $monsterCount,
                'groupMode' => $group_id > 0 ? 1 : 0,
                'pageType' => 'halloffamesingle',
                'search' => $search,
                'time_filter' => $time_filter,
                'skip' => $skip

            ]);
        } else {
            return view('error', [
                'error_message' => 'No monster found'
            ]);
        }
        
    }

    public function update(Request $request){

        if (Auth::check()){
            $user_id = Auth::User()->id;
            if ($user_id != 1) return;

            $action = $request->action;

            //Log::debug('action = '.$action);
            if ($action == 'flag'){
                $monster_id = $request->monster_id;
                $severity = $request->severity;

                $monster = Monster::find($monster_id);

                if ($severity == 'nsfl'){
                    $monster->nsfl = 1;
                    $monster->nsfw = 1;
                } else if ($severity == 'nsfw'){
                    $monster->nsfl = 0;
                    $monster->nsfw = 1;
                } else if ($severity == 'safe'){
                    $monster->nsfl = 0;
                    $monster->nsfw = 0;
                }

                $monster->save();
            } elseif ($action == 'rollback'){
                $monster_id = $request->monster_id;
                $segments = $request->segments;

                $monster = Monster::find($monster_id);

                if ($segments == 'legs'){
                    MonsterSegment::where('monster_id', $monster_id)
                        ->where('segment','legs')
                        ->delete();
                    $monster->status = 'awaiting legs';
                    $monster->image = NULL;
                }
                elseif ($segments == 'body_legs'){
                    MonsterSegment::where('monster_id', $monster_id)
                        ->whereIn('segment', ['body','legs'])
                        ->delete();
                    $monster->status = 'awaiting body';
                    $monster->image = NULL;
                }

                $monster->save();
            } elseif ($action == 'abort'){
                $monster_id = $request->monster_id;

                $monster = Monster::find($monster_id);
                $monster->status = 'cancelled';
                $monster->save();
            }
        }
    }
}
