<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Monster;
use App\MonsterSegment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class GalleryController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth','verified']);
    }

    //
    public function index($monster_id = NULL){

        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = User::find($user_id);
            if (!isset($user->email_verified_at)){
                $user = NULL;
            }
        } else {
            $user = NULL;
        }
        
        if (!isset($monster_id)) {
            $monster = Monster::without(['segments', 'ratings'])
                ->where('nsfl', '0')
                ->where('status','complete')
                ->when(!$user || $user->allow_nsfw == 0, function($q) {
                    $q->where('nsfw', '0');
                })
                ->orderBy('created_at', 'desc')
                ->get(['id'])
                ->first();
            $monster_id = $monster->id;
        }

        $monster = Monster::where('id',$monster_id)
            ->when(!$user || $user->id != 1, function($q) {
                $q->where('status','complete');
            })
            ->get()
            ->first();
        
        if ($monster){

            $nextMonster = Monster::without(['segments', 'ratings'])
                ->where('id','<>', $monster_id)
                ->where('created_at','>', $monster->created_at)
                ->where('nsfl', '0')
                ->when(!$user || $user->allow_nsfw == 0, function($q) {
                    $q->where('nsfw', '0');
                })
                ->where('status','complete')
                ->orderBy('created_at')
                ->get(['id','name'])
                ->first();
                
            $prevMonster = Monster::without(['segments', 'ratings'])
                ->where('id','<>', $monster_id)
                ->where('created_at','<', $monster->created_at)
                ->where('nsfl', '0')
                ->when(!$user || $user->allow_nsfw == 0, function($q) {
                    $q->where('nsfw', '0');
                })
                ->where('status','complete')
                ->orderBy('created_at', 'desc')
                ->get(['id','name'])
                ->first();
            
            return view('gallery', [
                'monster' => $monster,
                'user' => $user,
                'prevMonster' => $prevMonster ? $prevMonster : $monster,
                'nextMonster' => $nextMonster ? $nextMonster : $monster,
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
                }
                elseif ($segments == 'body_legs'){
                    MonsterSegment::where('monster_id', $monster_id)
                        ->whereIn('segment', ['body','legs'])
                        ->delete();
                    $monster->status = 'awaiting body';
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
