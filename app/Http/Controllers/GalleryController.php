<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use App\MonsterSegment;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    //
    public function index($monster_id = NULL){

        $user_id = $user_id = Auth::User()->id;
        
        if (isset($monster_id)) {
            $monster = Monster::find($monster_id);
        } else {
            $monster = Monster::where('status','complete')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->first();
            $monster_id = $monster->id;
        }
        
        if ($monster){

            $nextMonster = Monster::where('status','complete')
                ->where('id','<>', $monster_id)
                ->where('updated_at','>', $monster->updated_at)
                ->orderBy('updated_at')
                ->get();
                
            $prevMonster = Monster::where('status','complete')
                ->where('id','<>', $monster_id)
                ->where('updated_at','<', $monster->updated_at)
                ->orderBy('updated_at', 'desc')
                ->get();
              
            return view('gallery', [
                'monster' => $monster,
                'userId' => $user_id,
                'prevMonster' => count($prevMonster) ? $prevMonster->first() : $monster,
                'nextMonster' => count($nextMonster) ? $nextMonster->first() : $monster,
            ]);
        } else {
            return view('error', [
                'error_message' => 'No monster found'
            ]);
        }
    }
}
