<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MonsterSegment;
use App\Monster;

class CanvasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (isset($request->monster_id)){
            $monster_id = $request->monster_id;
            $monster_segments = MonsterSegment::find($monster_id);
            if (count($monster_segments) == 1){
                $monster_segment_name = 'body';
            } else {
                $monster_segment_name = 'legs';
            }

        } else {
            $monster_segment_name = 'head';
        }
        
        return view('canvas', [
            'segment_name' => $monster_segment_name
        ]);
    }

    public function save(Request $request)
    {
        if (isset($request->monster_id)){
            $monster_id = $monster_id;
        } else {
            //Create new monster
            $monster = new Monster;
            $monster->name = 'Default name';
            $monster->save();
            $monster_id = $monster->id;
        }

        $monster_segment = new MonsterSegment;
        $monster_segment->image = $request->imgBase64;
        $monster_segment->monster_id = $monster_id;
        $monster_segment->save();

        // return 'savedx';
    }
}
