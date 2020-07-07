<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use App\MonsterSegment;
use Carbon\Carbon;

class GalleryController extends Controller
{
    //
    public function index($monster_id = NULL){
        if (isset($monster_id)) {
            $monster = Monster::find($monster_id);
        } else {
            $monster = Monster::where('status','complete')->first()->get();
        }
        $monster['segments'] = $monster->segments;


        $nextMonster = Monster::where('status','complete')
            ->where('id','<>', $monster_id)
            ->whereDate('updated_at','>', $monster->updated_at)
            ->orderBy('updated_at')
            ->get();

        $prevMonster = Monster::where('status','complete')
            ->where('id','<>', $monster_id)
            ->whereDate('updated_at','<', $monster->updated_at)
            ->orderBy('updated_at')
            ->get();

        return view('gallery', [
            'monster' => $monster,
            'prevMonster' => count($prevMonster) ? $prevMonster->first() : $monster,
            'nextMonster' => count($nextMonster) ? $nextMonster->first() : $monster,
        ]);
    }
}
