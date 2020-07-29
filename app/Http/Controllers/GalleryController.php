<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Monster;
use App\MonsterSegment;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth','verified']);
    }

    //
    public function index($monster_id = NULL){

        if (Auth::check()){
            $user_id = $user_id = Auth::User()->id;
            $user = User::find($user_id);
            if (!isset($user->email_verified_at)){
                $user = NULL;
            }
        } else {
            $user = NULL;
        }
        
        if (isset($monster_id)) {
            $monster = Monster::find($monster_id);
        } else {
            $monster = Monster::where('status','complete')
                ->where('nsfl', '0')
                ->where('nsfw', '0')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->first();
            $monster_id = $monster->id;
        }
        
        if ($monster){

            $nextMonster = Monster::where('status','complete')
                ->where('id','<>', $monster_id)
                ->where('updated_at','>', $monster->updated_at)
                ->where('nsfl', '0')
                ->where('nsfw', '0')
                ->orderBy('updated_at')
                ->get();
                
            $prevMonster = Monster::where('status','complete')
                ->where('id','<>', $monster_id)
                ->where('updated_at','<', $monster->updated_at)
                ->where('nsfl', '0')
                ->where('nsfw', '0')
                ->orderBy('updated_at', 'desc')
                ->get();
            
            return view('gallery', [
                'monster' => $monster,
                'user' => $user,
                'prevMonster' => count($prevMonster) ? $prevMonster->first() : $monster,
                'nextMonster' => count($nextMonster) ? $nextMonster->first() : $monster,
            ]);
        } else {
            return view('error', [
                'error_message' => 'No monster found'
            ]);
        }
        
    }

    public function update(Request $request){

        if (Auth::check()){
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
        }
    }
}
